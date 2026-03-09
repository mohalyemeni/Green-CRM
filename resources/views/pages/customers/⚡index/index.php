<?php

use App\Livewire\Forms\CustomerForm;
use App\Models\Customer;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('Customers')] class extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';
    // Filters
    public $search = '';
    public $country = '';
    public $status = '';
    public $created_at = '';
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

            // 4. الفلترة حسب الدولة
            ->when($this->country, fn($q) => $q->where('country', 'LIKE', '%' . $this->country . '%'))

            // 5. معالجة الحالة (1 = نشط، 2 = غير نشط)
            ->when($this->status, function ($q) {
                // تحويل القيمة [1, 2] إلى القيمة المنطقية المتوافقة مع is_active
                return $q->where('status', $this->status == 1);
            })

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
