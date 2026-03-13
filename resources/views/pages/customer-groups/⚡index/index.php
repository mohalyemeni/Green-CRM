<?php

namespace App\Livewire;

use App\Livewire\Forms\CustomerGroupForm; // Form الخاص بمجموعات العملاء
use App\Models\CustomerGroup;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('مجموعات العملاء')] class extends Component
{
    use WithPagination, WithoutUrlPagination; // تمت إزالة WithFileUploads لعدم الحاجة لرفع ملفات هنا

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';  // فلتر مبسط

    // Offcanvas filters (فلاتر متقدمة)
    public $created_from = '';
    public $created_to = '';
    public $selectedStatuses = [];

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected groups for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $groupToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public CustomerGroupForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'             => ['except' => ''],
        'status'             => ['except' => ''],
        'selectedStatuses'   => ['except' => []],
        'created_from'       => ['except' => ''],
        'created_to'         => ['except' => ''],
        'sortField'          => ['except' => 'created_at'],
        'sortDirection'      => ['except' => 'desc'],
    ];

    #[Computed]
    public function customerGroupsList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['name', 'name_en', 'code', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return CustomerGroup::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين (المُنشئ والمُعدل) (Eager Loading)
            ->with(['creator', 'editor'])

            // 3. البحث الذكي (باستخدام الـ SearchableTrait الموجود في مودل CustomerGroup)
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الفلاتر
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })

            // 5. Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('customer_groups.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('customer_groups.created_at', '<=', $this->created_to))

            // 6. الترتيب: يُطبَّق دائماً
            ->orderBy($sortField, $this->sortDirection)

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($groupId)
    {
        $group = CustomerGroup::findOrFail($groupId);
        $group->status = $group->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $group->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة المجموعة بنجاح.');
        unset($this->customerGroupsList);
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
        // تصفير الفلاتر المتعلقة بالمجموعات
        $this->reset(['search', 'status', 'selectedStatuses', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function resetAndCloseFilters()
    {
        $this->resetFilters();
        $this->dispatch('close-offcanvas');
    }

    public function confirmDelete($groupId)
    {
        $this->groupToDelete = $groupId;
        $this->showDeleteModal = true;
    }

    public function deleteGroup()
    {
        if ($this->groupToDelete) {
            CustomerGroup::find($this->groupToDelete)->delete();
            $this->showDeleteModal = false;
            $this->groupToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف المجموعة بنجاح.');
            unset($this->customerGroupsList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->customerGroupsList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        CustomerGroup::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف المجموعات المحددة بنجاح.');
        unset($this->customerGroupsList);
    }

    // ===== إضافة مجموعة جديدة =====
    public function saveCustomerGroup(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة المجموعة بنجاح.');
        unset($this->customerGroupsList);
    }

    // ===== تحضير فورم التعديل =====
    public function editCustomerGroup(CustomerGroup $group): void
    {
        $this->form->setCustomerGroup($group);
        $this->dispatch('open-modal');
    }

    // ===== تحديث مجموعة موجودة =====
    public function updateCustomerGroup(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات المجموعة بنجاح.');
        unset($this->customerGroupsList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitCustomerGroup(): void
    {
        if ($this->form->customerGroup) {
            $this->updateCustomerGroup();
        } else {
            $this->saveCustomerGroup();
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
