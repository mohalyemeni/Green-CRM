<?php

namespace App\Livewire;

use App\Livewire\Forms\LeadsForm; // الفورم الجديد للعملاء
use App\Models\Lead;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('إدارة العملاء المحتملين')] class extends Component
{
    use WithPagination, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // الفلاتر الأساسية
    public $search = '';

    // فلاتر متقدمة (Offcanvas)
    public $created_from = '';
    public $created_to = '';
    public $selectedStatuses = [];
    public $selectedSources = [];
    public $selectedIndustries = [];
    public $selectedPriorities = []; // 1: High, 2: Medium, 3: Low

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // التحكم في العرض
    public $perPage = 10;
    public $selectedIds = [];
    public $selectAll = false;

    // حالة المودال
    public $showDeleteModal = false;
    public $leadToDelete = null;

    // كائن الفورم
    public LeadsForm $form;

    // الربط مع الرابط (URL) لضمان بقاء الفلترة عند تحديث الصفحة
    protected $queryString = [
        'search'           => ['except' => ''],
        'selectedStatuses' => ['except' => []],
        'selectedSources'  => ['except' => []],
        'selectedPriorities' => ['except' => []],
        'created_from'     => ['except' => ''],
        'created_to'       => ['except' => ''],
        'sortField'        => ['except' => 'created_at'],
        'sortDirection'    => ['except' => 'desc'],
    ];

    #[Computed]
    public function leadsList()
    {
        // حماية أمنية وتحديد حقول الترتيب
        $validSortFields = ['lead_number', 'first_name', 'estimated_value', 'priority', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Lead::query()
            // جلب العلاقات لتقليل استعلامات قاعدة البيانات (N+1 Problem)
            ->with(['status', 'source', 'industry', 'company', 'branch', 'owner', 'creator'])

            // البحث الذكي (الاسم، البريد، الجوال، رقم العميل)
            ->when($this->search, fn($q) => $q->search($this->search))

            // فلاتر الحالة والمصدر والقطاع
            ->when(!empty($this->selectedStatuses), fn($q) => $q->whereIn('lead_status_id', $this->selectedStatuses))
            ->when(!empty($this->selectedSources), fn($q) => $q->whereIn('lead_source_id', $this->selectedSources))
            ->when(!empty($this->selectedIndustries), fn($q) => $q->whereIn('industry_id', $this->selectedIndustries))
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
        $this->reset(['search', 'selectedStatuses', 'selectedSources', 'selectedIndustries', 'selectedPriorities', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($leadId)
    {
        $this->leadToDelete = $leadId;
        $this->showDeleteModal = true;
    }

    public function deleteLead()
    {
        if ($this->leadToDelete) {
            Lead::find($this->leadToDelete)->delete();
            $this->showDeleteModal = false;
            $this->leadToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم نقل العميل إلى سلة المهملات بنجاح.');
            unset($this->leadsList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->leadsList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Lead::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف العملاء المحددين بنجاح.');
        unset($this->leadsList);
    }

    // ===== حفظ عميل جديد =====
    public function saveLead(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة العميل المحتمل بنجاح.');
        unset($this->leadsList);
    }

    // ===== تحضير فورم التعديل =====
    public function editLead(Lead $lead): void
    {
        $this->form->setLead($lead);
        $this->dispatch('open-modal');
    }

    // ===== تحديث عميل موجود =====
    public function updateLead(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات العميل بنجاح.');
        unset($this->leadsList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) =====
    public function submitLead(): void
    {
        if ($this->form->lead) {
            $this->updateLead();
        } else {
            $this->saveLead();
        }
    }

    public function cancel(): void
    {
        $this->form->reset();
        $this->resetPage();
        $this->resetValidation();
    }
};
