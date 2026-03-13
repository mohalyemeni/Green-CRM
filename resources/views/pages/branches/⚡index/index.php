<?php

namespace App\Livewire;

use App\Livewire\Forms\BranchesForm; // استخدام فورم الفروع
use App\Models\Branch;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('بيانات الفروع')] class extends Component
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
    public $selectedCompanies = []; // فلتر جديد: البحث عن فروع تابعة لشركات محددة
    public $selectedCountries = []; // فلتر جديد: البحث عن فروع في دول محددة

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected branches for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $branchToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public BranchesForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'             => ['except' => ''],
        'status'             => ['except' => ''],
        'selectedCompanies'  => ['except' => []],
        'selectedCountries'  => ['except' => []],
        'selectedStatuses'   => ['except' => []],
        'created_from'       => ['except' => ''],
        'created_to'         => ['except' => ''],
        'sortField'          => ['except' => 'created_at'],
        'sortDirection'      => ['except' => 'desc'],
    ];

    #[Computed]
    public function branchesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها في الفروع
        $validSortFields = ['code', 'name', 'name_en', 'commercial_register', 'company_id', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Branch::query()
            // 2. تحسين الأداء: جلب العلاقات المرتبطة (الشركة، الدولة، العملة، المستخدمين)
            ->with(['company', 'country', 'currency', 'creator', 'editor'])

            // 3. البحث الذكي (يستخدم SearchableTrait الموجود في مودل Branch)
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الفلاتر
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })
            ->when(!empty($this->selectedCompanies), function ($q) {
                return $q->whereIn('company_id', $this->selectedCompanies);
            })
            ->when(!empty($this->selectedCountries), function ($q) {
                return $q->whereIn('country_id', $this->selectedCountries);
            })

            // 5. Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('branches.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('branches.created_at', '<=', $this->created_to))

            // 6. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($branchId)
    {
        $branch = Branch::findOrFail($branchId);
        $branch->status = $branch->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $branch->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة الفرع بنجاح.');
        unset($this->branchesList);
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
        // تصفير فلاتر الفروع
        $this->reset(['search', 'status', 'selectedCompanies', 'selectedCountries', 'selectedStatuses', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function resetAndCloseFilters()
    {
        $this->resetFilters();
        $this->dispatch('close-offcanvas');
    }

    public function confirmDelete($branchId)
    {
        $this->branchToDelete = $branchId;
        $this->showDeleteModal = true;
    }

    public function deleteBranch()
    {
        if ($this->branchToDelete) {
            Branch::find($this->branchToDelete)->delete();
            $this->showDeleteModal = false;
            $this->branchToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف الفرع بنجاح.');
            unset($this->branchesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->branchesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Branch::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف الفروع المحددة بنجاح.');
        unset($this->branchesList);
    }

    // ===== إضافة فرع جديد =====
    public function saveBranch(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة الفرع بنجاح.');
        unset($this->branchesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editBranch(Branch $branch): void
    {
        $this->form->setBranch($branch);
        $this->dispatch('open-modal');
    }

    // ===== تحديث فرع موجود =====
    public function updateBranch(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات الفرع بنجاح.');
        unset($this->branchesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitBranch(): void
    {
        if ($this->form->branch) {
            $this->updateBranch();
        } else {
            $this->saveBranch();
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
