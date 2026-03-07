<?php

use App\Models\Customer;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;

new #[Title('Customers')] class extends Component
{
    use WithPagination, WithFileUploads;

    // Filters
    public $search = '';
    public $country = '';
    public $status = '';
    public $sortField = 'created_at';
    public $sortDirection = 'asc';

    // Pagination
    public $perPage = 10;

    // Selected customers for bulk actions
    public $selected = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $customerToDelete = null;

    // Query string for URL persistence
    protected $queryString = [
        'search'        => ['except' => ''],
        'country'       => ['except' => ''],
        'status'        => ['except' => ''],
        'sortField'     => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'asc'],
    ];

    #[Computed]
    public function customers()
    {
        $validSortFields = ['name', 'customer_number', 'created_at', 'status', 'country', 'email', 'mobile'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Customer::query()
            ->with(['creator', 'editor'])
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->country, fn($q) => $q->where('country', $this->country))
            ->when($this->status, fn($q) => $q->where('status', $this->status == 1))
            ->when(!$this->search, fn($q) => $q->orderBy($sortField, $this->sortDirection))
            ->paginate($this->perPage);
    }

    #[Computed]
    public function countries()
    {
        return Customer::distinct('country')->pluck('country')->sort();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedCountry()
    {
        $this->resetPage();
    }
    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'country', 'status']);
        $this->resetPage();
    }

    public function confirmDelete($customerId)
    {
        $this->customerToDelete = $customerId;
        $this->showDeleteModal = true;
    }

    public function deleteCustomer()
    {
        if ($this->customerToDelete) {
            Customer::find($this->customerToDelete)->delete();
            $this->showDeleteModal = false;
            $this->customerToDelete = null;
            session()->flash('message', 'Customer deleted successfully.');
        }
    }

    public function updatedSelectAll($value)
    {
        $this->selected = $value
            ? $this->customers->pluck('id')->map(fn($id) => (string) $id)->toArray()
            : [];
    }

    public function bulkDelete()
    {
        Customer::whereIn('id', $this->selected)->delete();
        $this->selected  = [];
        $this->selectAll = false;
        session()->flash('message', 'Selected customers deleted successfully.');
    }
}; ?>

<div>
    {{-- Flash Message --}}
    @if (session()->has('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    {{-- Filters --}}
    <div class="mb-4 d-flex gap-2">
        <input type="text" wire:model.live.debounce.300ms="search"
            placeholder="Search..." class="form-control" />

        <select wire:model.live="country" class="form-select">
            <option value="">All Countries</option>
            @foreach($this->countries as $c)
            <option value="{{ $c }}">{{ $c }}</option>
            @endforeach
        </select>

        <select wire:model.live="status" class="form-select">
            <option value="">All Status</option>
            <option value="1">Active</option>
            <option value="2">Inactive</option>
        </select>

        <button wire:click="resetFilters" class="btn btn-secondary">Reset</button>
    </div>

    {{-- Table --}}
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th><input type="checkbox" wire:model.live="selectAll" /></th>
                    <th>#</th>
                    <th wire:click="sortBy('name')" style="cursor:pointer">
                        Name @if($sortField === 'name') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                    </th>
                    <th>Email</th>
                    <th wire:click="sortBy('country')" style="cursor:pointer">
                        Country @if($sortField === 'country') {{ $sortDirection === 'asc' ? '↑' : '↓' }} @endif
                    </th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->customers as $customer)
                <tr wire:key="customer-{{ $customer->id }}">
                    <td><input type="checkbox" wire:model.live="selected" value="{{ $customer->id }}" /></td>
                    <td>{{ $customer->customer_number }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->country }}</td>
                    <td>{{ $customer->status?->label() }}</td>
                    <td>
                        <button wire:click="confirmDelete({{ $customer->id }})"
                            class="btn btn-sm btn-danger">Delete</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No customers found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-3">
        {{ $this->customers->links() }}
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteModal)
    <div class="modal fade show d-block" style="background:rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                </div>
                <div class="modal-body">Are you sure you want to delete this customer?</div>
                <div class="modal-footer">
                    <button wire:click="deleteCustomer" class="btn btn-danger btn-sm">Yes, Delete</button>
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-secondary btn-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>