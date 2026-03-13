<?php

namespace App\Livewire;

use App\Livewire\Forms\LeadStatusesForm; // استخدام فورم حالات العملاء
use App\Models\LeadStatus;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('حالات العملاء المحتملين')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';  // فلتر الحالة (مفعل/معطل)

    // Offcanvas filters (فلاتر متقدمة)
    public $created_from = '';
    public $created_to = '';
    public $selectedStatuses = [];
    public $is_default = ''; // فلتر الحالة الافتراضية
    public $is_closed = '';  // فلتر حالات الإغلاق

    public $sortField = 'sort_order'; // الترتيب الافتراضي
    public $sortDirection = 'asc';

    // Pagination
    public $perPage = 10;

    // Selected items for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $statusToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public LeadStatusesForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'           => ['except' => ''],
        'status'           => ['except' => ''],
        'selectedStatuses' => ['except' => []],
        'is_default'       => ['except' => ''],
        'is_closed'        => ['except' => ''],
        'created_from'     => ['except' => ''],
        'created_to'       => ['except' => ''],
        'sortField'        => ['except' => 'sort_order'],
        'sortDirection'    => ['except' => 'asc'],
    ];

    #[Computed]
    public function leadStatusesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['name', 'name_en', 'code', 'sort_order', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'sort_order';

        return LeadStatus::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين
            ->with(['creator', 'editor'])

            // 3. البحث الذكي
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الفلاتر
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })

            // 5. فلاتر منطقية خاصة بالحالات
            ->when($this->is_default !== '', fn($q) => $q->where('is_default', $this->is_default))
            ->when($this->is_closed !== '', fn($q) => $q->where('is_closed', $this->is_closed))

            // 6. Filter By Dates
            ->when($this->created_from, fn($q) => $q->whereDate('lead_statuses.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('lead_statuses.created_at', '<=', $this->created_to))

            // 7. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 8. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($statusId)
    {
        $status = LeadStatus::findOrFail($statusId);
        $status->status = $status->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $status->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة التفعيل بنجاح.');
        unset($this->leadStatusesList);
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

    public function applyFilters()
    {
        $this->resetPage();
        $this->dispatch('close-offcanvas');
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'selectedStatuses', 'is_default', 'is_closed', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($statusId)
    {
        $this->statusToDelete = $statusId;
        $this->showDeleteModal = true;
    }

    public function deleteStatus()
    {
        if ($this->statusToDelete) {
            LeadStatus::find($this->statusToDelete)->delete();
            $this->showDeleteModal = false;
            $this->statusToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف حالة العميل بنجاح.');
            unset($this->leadStatusesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->leadStatusesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        LeadStatus::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف الحالات المحددة بنجاح.');
        unset($this->leadStatusesList);
    }

    // ===== إضافة حالة جديدة =====
    public function saveLeadStatus(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة الحالة بنجاح.');
        unset($this->leadStatusesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editLeadStatus(LeadStatus $leadStatus): void
    {
        $this->form->setLeadStatus($leadStatus);
        $this->dispatch('open-modal');
    }

    // ===== تحديث حالة موجودة =====
    public function updateLeadStatus(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات الحالة بنجاح.');
        unset($this->leadStatusesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) =====
    public function submitLeadStatus(): void
    {
        if ($this->form->leadStatus) {
            $this->updateLeadStatus();
        } else {
            $this->saveLeadStatus();
        }
    }

    public function cancel(): void
    {
        $this->form->reset();
        $this->resetPage();
        $this->resetValidation();
    }
};
