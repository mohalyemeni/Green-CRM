<?php

namespace App\Livewire;

use App\Livewire\Forms\OpportunitySourcesForm; // استخدام فورم مصادر الفرص
use App\Models\OpportunitySource;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('مصادر الفرص')] class extends Component
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

    public $sortField = 'sort_order'; // الترتيب الافتراضي حسب حقل الترتيب
    public $sortDirection = 'asc';

    // Pagination
    public $perPage = 10;

    // Selected sources for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $sourceToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public OpportunitySourcesForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'           => ['except' => ''],
        'status'           => ['except' => ''],
        'selectedStatuses' => ['except' => []],
        'created_from'     => ['except' => ''],
        'created_to'       => ['except' => ''],
        'sortField'        => ['except' => 'sort_order'],
        'sortDirection'    => ['except' => 'asc'],
    ];

    #[Computed]
    public function opportunitySourcesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['name', 'name_en', 'code', 'sort_order', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'sort_order';

        return OpportunitySource::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين (Eager Loading)
            ->with(['creator', 'editor'])

            // 3. البحث الذكي (باستخدام الـ SearchableTrait الموجود في المودل)
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الفلاتر
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })

            // 5. Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('opportunity_sources.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('opportunity_sources.created_at', '<=', $this->created_to))

            // 6. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($sourceId)
    {
        $source = OpportunitySource::findOrFail($sourceId);
        $source->status = $source->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $source->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة مصدر الفرصة بنجاح.');
        unset($this->opportunitySourcesList);
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
        $this->reset(['search', 'status', 'selectedStatuses', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function resetAndCloseFilters()
    {
        $this->resetFilters();
        $this->dispatch('close-offcanvas');
    }

    public function confirmDelete($sourceId)
    {
        $this->sourceToDelete = $sourceId;
        $this->showDeleteModal = true;
    }

    public function deleteSource()
    {
        if ($this->sourceToDelete) {
            OpportunitySource::find($this->sourceToDelete)->delete();
            $this->showDeleteModal = false;
            $this->sourceToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف مصدر الفرصة بنجاح.');
            unset($this->opportunitySourcesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->opportunitySourcesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        OpportunitySource::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف المصادر المحددة بنجاح.');
        unset($this->opportunitySourcesList);
    }

    // ===== إضافة مصدر جديد =====
    public function saveOpportunitySource(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة مصدر الفرصة بنجاح.');
        unset($this->opportunitySourcesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editOpportunitySource(OpportunitySource $source): void
    {
        $this->form->setOpportunitySource($source);
        $this->dispatch('open-modal');
    }

    // ===== تحديث مصدر موجود =====
    public function updateOpportunitySource(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات المصدر بنجاح.');
        unset($this->opportunitySourcesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitOpportunitySource(): void
    {
        if ($this->form->opportunitySource) {
            $this->updateOpportunitySource();
        } else {
            $this->saveOpportunitySource();
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
