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
    public $sortField = 'first_name';
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
        'search' => ['except' => ''],
        'country' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'first_name'],
        'sortDirection' => ['except' => 'asc'],
    ];

    #[Computed]
    public function customers()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['name', 'customer_number', 'created_at', 'city', 'country'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Customer::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين المرتبطين دفعة واحدة (Eager Loading)
            ->with(['creator', 'editor'])

            // 3. البحث الذكي (يحتوي على ترتيب داخلي حسب الأهمية)
            ->when($this->search, fn($q) => $q->search($this->search))

            // 4. الفلترة حسب الدولة
            ->when($this->country, fn($q) => $q->where('country', $this->country))

            // 5. معالجة الحالة (1 = نشط، 2 = غير نشط)
            ->when($this->status, function ($q) {
                // تحويل القيمة [1, 2] إلى القيمة المنطقية المتوافقة مع is_active
                return $q->where('status', $this->status == 1);
            })

            // 6. الترتيب: يطبق فقط في حال عدم وجود بحث للحفاظ على دقة نتائج البحث
            ->when(!$this->search, fn($q) => $q->orderBy($sortField, $this->sortDirection))

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    #[Computed]
    public function countries()
    {
        return Customer::distinct('country')
            ->pluck('country')
            ->sort();
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

    public function updatedDepartment()
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
        if ($value) {
            $this->selected = $this->customers->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function bulkDelete()
    {
        Customer::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;

        session()->flash('message', 'Selected customers deleted successfully.');
    }

    private function getFilteredCustomers()
    {
        return Customer::query()
            ->when($this->search, fn($q) => $q->search($this->search))
            ->when($this->department, fn($q) => $q->where('department', $this->department))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection)
            ->get();
    }
};
