<?php

namespace App\Livewire;

use App\Livewire\Forms\OpportunitiesForm; // الفورم الخاص بالفرص البيعية
use App\Models\Opportunity;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('إدارة الفرص البيعية')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // الفلاتر الأساسية
    public $search = '';

    // فلاتر متقدمة (Offcanvas)
    public $created_from = '';
    public $created_to = '';
    public $selectedStages = [];     // فلتر حسب مرحلة المبيعات (Stage)
    public $selectedSources = [];    // فلتر حسب المصدر
    public $selectedPriorities = []; // فلتر حسب الأولوية (low, medium, high, urgent)

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // التحكم في العرض
    public $perPage = 10;
    public $selectedIds = [];
    public $selectAll = false;

    // حالة المودال
    public $showDeleteModal = false;
    public $opportunityToDelete = null;

    // كائن الفورم
    public OpportunitiesForm $form;

    // الربط مع الرابط (URL) لضمان بقاء الفلترة عند تحديث الصفحة
    protected $queryString = [
        'search'             => ['except' => ''],
        'selectedStages'     => ['except' => []],
        'selectedSources'    => ['except' => []],
        'selectedPriorities' => ['except' => []],
        'created_from'       => ['except' => ''],
        'created_to'         => ['except' => ''],
        'sortField'          => ['except' => 'created_at'],
        'sortDirection'      => ['except' => 'desc'],
    ];

    #[Computed]
    public function opportunitiesList()
    {
        // حماية أمنية وتحديد حقول الترتيب المسموح بها للفرص
        $validSortFields = ['opportunity_number', 'title', 'expected_revenue', 'probability', 'expected_close_date', 'priority', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Opportunity::query()
            // جلب العلاقات لتقليل استعلامات قاعدة البيانات (Eager Loading) بناءً على موديل الفرص
            ->with(['customer', 'company', 'pipeline', 'stage', 'source', 'currency', 'assignee', 'creator'])

            // البحث الذكي (العنوان، الرقم المرجعي، الوصف)
            ->when($this->search, fn($q) => $q->search($this->search))

            // فلاتر المراحل والمصادر والأولويات
            ->when(!empty($this->selectedStages), fn($q) => $q->whereIn('stage_id', $this->selectedStages))
            ->when(!empty($this->selectedSources), fn($q) => $q->whereIn('opportunity_source_id', $this->selectedSources))
            ->when(!empty($this->selectedPriorities), fn($q) => $q->whereIn('priority', $this->selectedPriorities))

            // فلترة التاريخ
            ->when($this->created_from, fn($q) => $q->whereDate('created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('created_at', '<=', $this->created_to))

            ->orderBy($sortField, $this->sortDirection)
            ->paginate($this->perPage);
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
        $this->reset(['search', 'selectedStages', 'selectedSources', 'selectedPriorities', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($opportunityId)
    {
        $this->opportunityToDelete = $opportunityId;
        $this->showDeleteModal = true;
    }

    public function deleteOpportunity()
    {
        if ($this->opportunityToDelete) {
            Opportunity::find($this->opportunityToDelete)->delete();
            $this->showDeleteModal = false;
            $this->opportunityToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم نقل الفرصة البيعية إلى سلة المهملات بنجاح.');
            unset($this->opportunitiesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->opportunitiesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Opportunity::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف الفرص المحددة بنجاح.');
        unset($this->opportunitiesList);
    }

    // ===== حفظ فرصة جديدة =====
    public function saveOpportunity(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة الفرصة البيعية بنجاح.');
        unset($this->opportunitiesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editOpportunity(Opportunity $opportunity): void
    {
        $this->form->setOpportunity($opportunity);
        $this->dispatch('open-modal');
    }

    // ===== تحديث فرصة موجودة =====
    public function updateOpportunity(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات الفرصة بنجاح.');
        unset($this->opportunitiesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) =====
    public function submitOpportunity(): void
    {
        if ($this->form->opportunity) {
            $this->updateOpportunity();
        } else {
            $this->saveOpportunity();
        }
    }

    public function cancel(): void
    {
        $this->form->reset();
        $this->resetPage();
        $this->resetValidation();
    }
};
