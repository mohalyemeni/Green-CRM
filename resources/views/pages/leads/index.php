<?php

namespace App\Livewire\Pages\Leads;

use App\Livewire\Forms\LeadForm;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\LeadStatus;
use App\Models\User;
use App\Enums\ActiveStatus;
use App\Enums\PriorityLevel;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;
use Carbon\Carbon;

new #[Title('العملاء المحتملين')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';

    // Offcanvas filters
    public $created_from      = '';
    public $created_to        = '';
    public $selectedSources   = [];
    public $selectedAssignees = [];
    public $selectedPriorities = [];
    public $selectedStatuses  = [];
    public $withoutComments48h = false;

    public $sortField     = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Bulk actions
    public $selectedIds = [];
    public $selectAll   = false;

    // Delete Modal
    public $showDeleteModal = false;
    public $leadToDelete    = null;

    // Form Object
    public LeadForm $form;

    protected $queryString = [
        'search'            => ['except' => ''],
        'selectedSources'   => ['except' => []],
        'selectedAssignees' => ['except' => []],
        'selectedPriorities' => ['except' => []],
        'created_from'      => ['except' => ''],
        'created_to'        => ['except' => ''],
        'sortField'         => ['except' => 'created_at'],
        'sortDirection'     => ['except' => 'desc'],
    ];

    #[Computed]
    public function leadsList()
    {
        $validSortFields = ['first_name', 'mobile', 'email', 'priority', 'created_at', 'lead_status_id'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        $query = Lead::query()
            ->with(['source', 'status', 'assignee', 'creator'])
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when(!empty($this->selectedSources),    fn($q) => $q->whereIn('lead_source_id', $this->selectedSources))
            ->when(!empty($this->selectedAssignees),  fn($q) => $q->whereIn('assigned_to', $this->selectedAssignees))
            ->when(!empty($this->selectedPriorities), fn($q) => $q->whereIn('priority', $this->selectedPriorities))
            ->when(!empty($this->selectedStatuses),   fn($q) => $q->whereIn('lead_status_id', $this->selectedStatuses))
            ->when($this->created_from, fn($q) => $q->whereDate('created_at', '>=', $this->created_from))
            ->when($this->created_to,   fn($q) => $q->whereDate('created_at', '<=', $this->created_to))
            ->when($this->withoutComments48h, function ($q) {
                $q->whereDoesntHave('comments', fn($cq) => $cq->where('created_at', '>=', Carbon::now()->subHours(48)));
            })
            ->orderBy($sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return $query;
    }

    #[Computed]
    public function sources()
    {
        return LeadSource::where('status', ActiveStatus::ACTIVE)->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function statuses()
    {
        return LeadStatus::where('status', ActiveStatus::ACTIVE->value)->orderBy('sort_order')->get(['id', 'name', 'color', 'is_closed']);
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
    }

    #[Computed]
    public function priorities()
    {
        return PriorityLevel::cases();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
        $this->dispatch('close-offcanvas');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'selectedSources', 'selectedAssignees', 'selectedPriorities', 'selectedStatuses', 'created_from', 'created_to', 'withoutComments48h']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($leadId)
    {
        $this->leadToDelete   = $leadId;
        $this->showDeleteModal = true;
    }

    public function deleteLead()
    {
        if ($this->leadToDelete) {
            Lead::find($this->leadToDelete)?->delete();
            $this->showDeleteModal = false;
            $this->leadToDelete   = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف العميل المحتمل بنجاح.');
            unset($this->leadsList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = collect($this->leadsList->items())->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Lead::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف العملاء المحددين بنجاح.');
        unset($this->leadsList);
    }

    public function render()
    {
        return view('pages.leads.index');
    }
};
