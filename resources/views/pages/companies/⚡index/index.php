<?php

namespace App\Livewire;

use App\Livewire\Forms\CompaniesForm; // بافتراض أنك أنشأت فورم خاص بالشركات
use App\Models\Company;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('بيانات الأنشطة التجارية')] class extends Component
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
    public $selectedCurrencies = []; // فلتر للبحث عن الشركات حسب عملتها الأساسية

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected companies for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $companyToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public CompaniesForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'             => ['except' => ''],
        'status'             => ['except' => ''],
        'selectedCurrencies' => ['except' => []],
        'selectedStatuses'   => ['except' => []],
        'created_from'       => ['except' => ''],
        'created_to'         => ['except' => ''],
        'sortField'          => ['except' => 'created_at'],
        'sortDirection'      => ['except' => 'desc'],
    ];

    #[Computed]
    public function companiesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['name', 'name_en', 'short_name', 'slug', 'base_currency_id', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Company::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين والعملة الأساسية المرتبطة (Eager Loading)
            ->with(['creator', 'editor', 'baseCurrency'])

            // 3. البحث الذكي (باستخدام الـ SearchableTrait الموجود في المودل)
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الفلاتر
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })
            ->when(!empty($this->selectedCurrencies), function ($q) {
                return $q->whereIn('base_currency_id', $this->selectedCurrencies);
            })

            // 5. Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('companies.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('companies.created_at', '<=', $this->created_to))

            // 6. الترتيب: يُطبَّق دائماً
            ->orderBy($sortField, $this->sortDirection)

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($companyId)
    {
        $company = Company::findOrFail($companyId);
        $company->status = $company->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $company->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة النشاط التجاري بنجاح.');
        unset($this->companiesList);
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
        $this->reset(['search', 'status', 'selectedCurrencies', 'selectedStatuses', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function resetAndCloseFilters()
    {
        $this->resetFilters();
        $this->dispatch('close-offcanvas');
    }

    public function confirmDelete($companyId)
    {
        $this->companyToDelete = $companyId;
        $this->showDeleteModal = true;
    }

    public function deleteCompany()
    {
        if ($this->companyToDelete) {
            Company::find($this->companyToDelete)->delete();
            $this->showDeleteModal = false;
            $this->companyToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف النشاط التجاري بنجاح.');
            unset($this->companiesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->companiesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Company::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف الأنشطة التجارية المحددة بنجاح.');
        unset($this->companiesList);
    }

    // ===== إضافة نشاط تجاري جديد =====
    public function saveCompany(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة النشاط التجاري بنجاح.');
        unset($this->companiesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editCompany(Company $company): void
    {
        $this->form->setCompany($company);
        $this->dispatch('open-modal');
    }

    // ===== تحديث نشاط تجاري موجود =====
    public function updateCompany(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات النشاط التجاري بنجاح.');
        unset($this->companiesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitCompany(): void
    {
        if ($this->form->company) {
            $this->updateCompany();
        } else {
            $this->saveCompany();
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
