<?php

namespace App\Livewire;

use App\Livewire\Forms\CustomerForm; // استخدام فورم العملاء
use App\Models\Customer;
use App\Models\Country;
use App\Enums\CustomerStatus;
use App\Enums\ActiveStatus; // مستخدم لفلترة الدول فقط إن وجد
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
    public $status = '';  // فلتر مبسط

    // Offcanvas filters (فلاتر متقدمة)
    public $created_from = '';
    public $created_to = '';
    public $selectedStatuses = [];
    public $selectedGenders = []; // فلتر البحث حسب الجنس
    public $selectedCountries = []; // فلتر البحث عن عملاء في دول محددة

    public $sortField = 'created_at'; // الترتيب الافتراضي
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
        'search'             => ['except' => ''],
        'status'             => ['except' => ''],
        'selectedCountries'  => ['except' => []],
        'selectedStatuses'   => ['except' => []],
        'selectedGenders'    => ['except' => []],
        'created_from'       => ['except' => ''],
        'created_to'         => ['except' => ''],
        'sortField'          => ['except' => 'created_at'],
        'sortDirection'      => ['except' => 'desc'],
    ];

    #[Computed]
    public function customersList()
    {
        $validSortFields = ['customer_number', 'name', 'email', 'mobile', 'phone', 'status', 'created_at', 'country_id'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Customer::query()
            // 2. تحسين الأداء: Eager Loading
            ->with(['creator', 'editor', 'country']) // تم إضافة علاقة country

            // 3. البحث الذكي (باستخدام SearchableTrait)
            ->when($this->search, fn($q) => $q->search($this->search))

            // 4. فلترة الحالة
            ->when($this->status !== '', fn($q) => $q->where('status', (int) $this->status))
            ->when(!empty($this->selectedStatuses), fn($q) => $q->whereIn('status', array_map('intval', $this->selectedStatuses)))

            // 5. فلترة الجنس والدول
            ->when(!empty($this->selectedGenders), fn($q) => $q->whereIn('gender', array_map('intval', $this->selectedGenders)))
            ->when(!empty($this->selectedCountries), fn($q) => $q->whereIn('country_id', $this->selectedCountries))

            // 6. Filter By Dates
            ->when($this->created_from, fn($q) => $q->whereDate('created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('created_at', '<=', $this->created_to))

            // 7. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 8. الترقيم
            ->paginate($this->perPage);
    }

    #[Computed]
    public function countries()
    {
        // جلب الدول المفعلة لعرضها في قوائم الفلترة والـ Form
        return Country::where('status', ActiveStatus::ACTIVE->value)
            ->whereNotNull('name')
            ->pluck('name', 'id')
            ->sort();
    }

    public function toggleStatus($customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $customer->status = $customer->status === CustomerStatus::ACTIVE
            ? CustomerStatus::INACTIVE
            : CustomerStatus::ACTIVE;

        $customer->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة العميل بنجاح.');
        unset($this->customersList);
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
        // تصفير فلاتر العملاء
        $this->reset(['search', 'status', 'selectedGenders', 'selectedCountries', 'selectedStatuses', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function resetAndCloseFilters()
    {
        $this->resetFilters();
        $this->dispatch('close-offcanvas');
    }

    public function confirmDelete($customerId)
    {
        $this->customerToDelete = $customerId;
        $this->showDeleteModal = true;
    }

    public function deleteCustomer()
    {
        if ($this->customerToDelete) {
            Customer::find($this->customerToDelete)?->delete();
            $this->showDeleteModal = false;
            $this->customerToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف العميل بنجاح.');
            unset($this->customersList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->customersList->pluck('id')->map(fn($id) => (string)$id)->toArray();
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
        unset($this->customersList);
    }

    // ===== إضافة عميل جديد =====
    public function saveCustomer(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة العميل بنجاح.');
        unset($this->customersList);
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
        unset($this->customersList);
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
        $this->resetPage();
        $this->resetValidation();
    }
};
