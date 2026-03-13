<?php

namespace App\Livewire;

use App\Livewire\Forms\IndustriesForm; // استخدام فورم القطاعات
use App\Models\Industry;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('بيانات القطاعات الأقتصادية')] class extends Component
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

    public $sortField = 'sort_order'; // الترتيب الافتراضي حسب حقل الترتيب المخصص
    public $sortDirection = 'asc';

    // Pagination
    public $perPage = 10;

    // Selected industries for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $industryToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public IndustriesForm $form;

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
    public function industriesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['name', 'name_en', 'sort_order', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'sort_order';

        return Industry::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين المرتبطين (Eager Loading)
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
            ->when($this->created_from, fn($q) => $q->whereDate('industries.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('industries.created_at', '<=', $this->created_to))

            // 6. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($industryId)
    {
        $industry = Industry::findOrFail($industryId);
        $industry->status = $industry->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $industry->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة القطاع بنجاح.');
        unset($this->industriesList);
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

    public function confirmDelete($industryId)
    {
        $this->industryToDelete = $industryId;
        $this->showDeleteModal = true;
    }

    public function deleteIndustry()
    {
        if ($this->industryToDelete) {
            Industry::find($this->industryToDelete)->delete();
            $this->showDeleteModal = false;
            $this->industryToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف القطاع بنجاح.');
            unset($this->industriesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->industriesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Industry::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف القطاعات المحددة بنجاح.');
        unset($this->industriesList);
    }

    // ===== إضافة قطاع جديد =====
    public function saveIndustry(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة القطاع بنجاح.');
        unset($this->industriesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editIndustry(Industry $industry): void
    {
        $this->form->setIndustry($industry);
        $this->dispatch('open-modal');
    }

    // ===== تحديث قطاع موجود =====
    public function updateIndustry(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات القطاع بنجاح.');
        unset($this->industriesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitIndustry(): void
    {
        if ($this->form->industry) {
            $this->updateIndustry();
        } else {
            $this->saveIndustry();
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
