<?php

namespace App\Livewire;

use App\Models\Quotation;
use App\Enums\QuotationStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('عروض الأسعار')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';

    // Offcanvas filters
    public $created_from        = '';
    public $created_to          = '';
    public $selectedStatuses    = [];
    public $customer_id         = '';

    public $sortField     = 'created_at';
    public $sortDirection = 'desc';

    public $perPage = 10;

    // Selected items for bulk actions
    public $selectedIds = [];
    public $selectAll   = false;

    // Modal state for delete
    public $showDeleteModal = false;
    public $quotationToDelete = null;

    // Query string for URL persistence
    protected $queryString = [
        'search'           => ['except' => ''],
        'status'           => ['except' => ''],
        'selectedStatuses' => ['except' => []],
        'customer_id'      => ['except' => ''],
        'created_from'     => ['except' => ''],
        'created_to'       => ['except' => ''],
        'sortField'        => ['except' => 'created_at'],
        'sortDirection'    => ['except' => 'desc'],
    ];

    #[Computed]
    public function quotationsList()
    {
        $validSortFields = ['code', 'title', 'customer_name', 'total', 'status', 'issue_date', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Quotation::query()
            ->with(['customer', 'creator'])
            ->when($this->search, function ($q) {
                $q->where(function($query) {
                    $query->where('code', 'like', '%' . $this->search . '%')
                          ->orWhere('title', 'like', '%' . $this->search . '%')
                          ->orWhere('customer_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->status !== '', fn($q) => $q->where('status', $this->status))
            ->when(!empty($this->selectedStatuses), fn($q) => $q->whereIn('status', $this->selectedStatuses))
            ->when($this->customer_id !== '', fn($q) => $q->where('customer_id', $this->customer_id))
            ->when($this->created_from, fn($q) => $q->whereDate('issue_date', '>=', $this->created_from))
            ->when($this->created_to,   fn($q) => $q->whereDate('issue_date', '<=', $this->created_to))
            ->orderBy($sortField, $this->sortDirection)
            ->paginate($this->perPage);
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

    public function updatedSearch() { $this->resetPage(); }
    public function updatedStatus() { $this->resetPage(); }

    public function applyFilters()
    {
        $this->resetPage();
        $this->dispatch('close-offcanvas');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'selectedStatuses', 'customer_id', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($id)
    {
        $this->quotationToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteQuotation()
    {
        if ($this->quotationToDelete) {
            Quotation::find($this->quotationToDelete)->delete();
            $this->showDeleteModal = false;
            $this->quotationToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف عرض السعر بنجاح.');
            unset($this->quotationsList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = collect($this->quotationsList->items())->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Quotation::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف عروض الأسعار المحددة بنجاح.');
        unset($this->quotationsList);
    }
};