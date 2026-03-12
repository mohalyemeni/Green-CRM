<?php

use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('بيانات العملاء')] class extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';
    // Filters
    public $search = '';
    public $country = ''; // For generic simple filter if any
    public $status = '';  // For generic simple filter if any
    public $created_at = '';

    // Offcanvas filters
    public $created_from = '';
    public $created_to = '';
    public $selectedCountries = [];
    public $selectedStatuses = [];

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected customers for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $customerToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public CustomerForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'        => ['except' => ''],
        'country'       => ['except' => ''],
        'status'        => ['except' => ''],
        'created_from'  => ['except' => ''],
        'created_to'    => ['except' => ''],
        'selectedCountries' => ['except' => []],
        'selectedStatuses'  => ['except' => []],
        'sortField'     => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
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
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. الفلترة حسب الدولة (Single string from past + Array matching from offcanvas)
            ->when($this->country, fn($q) => $q->where('country', 'LIKE', '%' . $this->country . '%'))
            ->when(!empty($this->selectedCountries), fn($q) => $q->whereIn('country', $this->selectedCountries))

            // 5. معالجة الحالة (Single status + Multiple from Offcanvas)
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status == 1);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })

            // Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('created_at', '<=', $this->created_to))

            // 6. الترتيب: يُطبَّق دائماً (مع الحماية الأمنية)
            ->orderBy($sortField, $this->sortDirection)

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

    public function applyFilters()
    {
        $this->resetPage();
        $this->dispatch('close-offcanvas');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'country', 'status', 'created_from', 'created_to', 'selectedCountries', 'selectedStatuses']);
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
            $this->dispatch('notify', type: 'warning', message: 'تم حذف العميل بنجاح.');
            unset($this->customers);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->customers->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Customer::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف العملاء المحددين بنجاح.');
        unset($this->customers);
    }

    // ===== إضافة عميل جديد =====
    public function saveCustomer(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة العميل بنجاح.');
        unset($this->customers);
    }

    // ===== تحضير فورم التعديل =====
    public function editCustomer(Customer $customer): void
    {
        $this->form->setCustomer($customer);
        $this->dispatch('open-modal');
    }

    // ===== تحديث عميل موجود =====
    public function updateCustomer(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات العميل بنجاح.');
        unset($this->customers);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitCustomer(): void
    {
        if ($this->form->customer) {
            $this->updateCustomer();
        } else {
            $this->saveCustomer();
        }
    }

    // ===== مسح الفورم عند الإلغاء أو إغلاق المودل =====
    public function cancel(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }
};
