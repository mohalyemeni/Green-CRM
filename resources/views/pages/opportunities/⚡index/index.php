<?php

namespace App\Livewire\Pages\Opportunities;

use App\Livewire\Forms\OpportunityForm;
use App\Models\Opportunity;
use App\Models\OpportunitySource;
use App\Models\PipelineStage;
use App\Models\User;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;
use Carbon\Carbon;

new #[Title('الفرص البيعية')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';

    // Offcanvas Filters
    public $created_from        = '';
    public $created_to          = '';
    public $selectedSources     = [];
    public $selectedAssignees   = [];
    public $selectedPriorities  = [];
    public $selectedStages      = [];
    public $withoutComments48h  = false;

    public $sortField     = 'created_at';
    public $sortDirection = 'desc';
    public $perPage       = 10;

    // Bulk Actions
    public $selectedIds = [];
    public $selectAll   = false;

    // Delete Modal
    public $showDeleteModal   = false;
    public $opportunityToDelete = null;

    // Form Object
    public OpportunityForm $form;

    protected $queryString = [
        'search'            => ['except' => ''],
        'selectedSources'   => ['except' => []],
        'selectedAssignees' => ['except' => []],
        'selectedPriorities'=> ['except' => []],
        'selectedStages'    => ['except' => []],
        'created_from'      => ['except' => ''],
        'created_to'        => ['except' => ''],
        'sortField'         => ['except' => 'created_at'],
        'sortDirection'     => ['except' => 'desc'],
    ];

    #[Computed]
    public function opportunitiesList()
    {
        $validSortFields = ['title', 'expected_revenue', 'probability', 'priority', 'created_at', 'stage_id'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        $query = Opportunity::query()
            ->with(['source', 'stage', 'assignee', 'customer', 'lostReason'])
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when(!empty($this->selectedSources),    fn($q) => $q->whereIn('opportunity_source_id', $this->selectedSources))
            ->when(!empty($this->selectedAssignees),  fn($q) => $q->whereIn('assigned_to', $this->selectedAssignees))
            ->when(!empty($this->selectedPriorities), fn($q) => $q->whereIn('priority', $this->selectedPriorities))
            ->when(!empty($this->selectedStages),     fn($q) => $q->whereIn('stage_id', $this->selectedStages))
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
        return OpportunitySource::where('status', ActiveStatus::ACTIVE)->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function stages()
    {
        return PipelineStage::where('status', ActiveStatus::ACTIVE)->orderBy('sort_order')->get(['id', 'name', 'color', 'is_won', 'is_lost']);
    }

    #[Computed]
    public function users()
    {
        return User::orderBy('first_name')->get(['id', 'first_name', 'last_name']);
    }

    #[Computed]
    public function priorities()
    {
        return [
            ['value' => 'low',    'label' => 'منخفضة', 'color' => 'info'],
            ['value' => 'medium', 'label' => 'متوسطة', 'color' => 'primary'],
            ['value' => 'high',   'label' => 'عالية',  'color' => 'warning'],
            ['value' => 'urgent', 'label' => 'عاجلة',  'color' => 'danger'],
        ];
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
        $this->reset(['search', 'selectedSources', 'selectedAssignees', 'selectedPriorities', 'selectedStages', 'created_from', 'created_to', 'withoutComments48h']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($opportunityId)
    {
        $this->opportunityToDelete = $opportunityId;
        $this->showDeleteModal     = true;
    }

    public function deleteOpportunity()
    {
        if ($this->opportunityToDelete) {
            Opportunity::find($this->opportunityToDelete)?->delete();
            $this->showDeleteModal     = false;
            $this->opportunityToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف الفرصة البيعية بنجاح.');
            unset($this->opportunitiesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = collect($this->opportunitiesList->items())->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Opportunity::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف الفرص المحددة بنجاح.');
        unset($this->opportunitiesList);
    }

    public function render()
    {
        return view('pages.opportunities.⚡index.index');
    }
};
