<?php

namespace App\Livewire;

use App\Livewire\Forms\PipelineStagesForm; // الفورم المحدث للمراحل
use App\Models\PipelineStage;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('مراحل تدفق المبيعات')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';  // فلتر مبسط

    // Offcanvas filters (فلاتر متقدمة)
    public $created_from = '';
    public $created_to = '';
    public $selectedStatuses = [];
    public $is_won = ''; // فلتر لمراحل الفوز
    public $is_lost = ''; // فلتر لمراحل الخسارة

    public $sortField = 'sort_order'; // الترتيب الافتراضي حسب حقل الترتيب
    public $sortDirection = 'asc';

    // Pagination
    public $perPage = 10;

    // Selected stages for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $stageToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public PipelineStagesForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'           => ['except' => ''],
        'status'           => ['except' => ''],
        'selectedStatuses' => ['except' => []],
        'is_won'           => ['except' => ''],
        'is_lost'          => ['except' => ''],
        'created_from'     => ['except' => ''],
        'created_to'       => ['except' => ''],
        'sortField'        => ['except' => 'sort_order'],
        'sortDirection'    => ['except' => 'asc'],
    ];

    #[Computed]
    public function pipelineStagesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['name', 'name_en', 'code', 'probability', 'sort_order', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'sort_order';

        return PipelineStage::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين (Eager Loading)
            ->with(['creator', 'editor'])

            // 3. البحث الذكي (باستخدام الـ SearchableTrait)
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الفلاتر الأساسية
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })

            // 5. فلاتر منطقية خاصة بالمراحل
            ->when($this->is_won !== '', fn($q) => $q->where('is_won', $this->is_won))
            ->when($this->is_lost !== '', fn($q) => $q->where('is_lost', $this->is_lost))

            // 6. Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('pipeline_stages.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('pipeline_stages.created_at', '<=', $this->created_to))

            // 7. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 8. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($stageId)
    {
        $stage = PipelineStage::findOrFail($stageId);
        $stage->status = $stage->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $stage->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة المرحلة بنجاح.');
        unset($this->pipelineStagesList);
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
        $this->reset(['search', 'status', 'selectedStatuses', 'is_won', 'is_lost', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function resetAndCloseFilters()
    {
        $this->resetFilters();
        $this->dispatch('close-offcanvas');
    }

    public function confirmDelete($stageId)
    {
        $this->stageToDelete = $stageId;
        $this->showDeleteModal = true;
    }

    public function deleteStage()
    {
        if ($this->stageToDelete) {
            PipelineStage::find($this->stageToDelete)->delete();
            $this->showDeleteModal = false;
            $this->stageToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف المرحلة بنجاح.');
            unset($this->pipelineStagesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->pipelineStagesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        PipelineStage::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف المراحل المحددة بنجاح.');
        unset($this->pipelineStagesList);
    }

    // ===== إضافة مرحلة جديدة =====
    public function saveStage(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة المرحلة بنجاح.');
        unset($this->pipelineStagesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editStage(PipelineStage $stage): void
    {
        $this->form->setStage($stage);
        $this->dispatch('open-modal');
    }

    // ===== تحديث مرحلة موجودة =====
    public function updateStage(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات المرحلة بنجاح.');
        unset($this->pipelineStagesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) =====
    public function submitStage(): void
    {
        if ($this->form->pipelineStage) {
            $this->updateStage();
        } else {
            $this->saveStage();
        }
    }

    // ===== مسح الفورم =====
    public function cancel(): void
    {
        $this->form->reset();
        $this->resetPage();
        $this->resetValidation();
    }
};
