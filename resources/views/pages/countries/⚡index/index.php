<?php

namespace App\Livewire;

use App\Livewire\Forms\CountriesForm; // Form الخاص بالدول
use App\Models\Country;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads; // ابقيتها في حال أردت مستقبلاً إضافة صورة لعلم الدولة (Flag)
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('بيانات الدول')] class extends Component
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

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected countries for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $countryToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public CountriesForm $form;

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
    public function countriesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها (تم تعديلها لجدول الدول)
        $validSortFields = ['name', 'name_en', 'country_code', 'phone_code', 'nationality', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Country::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين (المُنشئ والمُعدل) (Eager Loading)
            ->with(['creator', 'editor'])

            // 3. البحث الذكي (باستخدام الـ SearchableTrait الموجود في مودل Country)
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الفلاتر
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })

            // 5. Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('countries.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('countries.created_at', '<=', $this->created_to))

            // 6. الترتيب: يُطبَّق دائماً
            ->orderBy($sortField, $this->sortDirection)

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($countryId)
    {
        $country = Country::findOrFail($countryId);
        $country->status = $country->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $country->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة الدولة بنجاح.');
        unset($this->countriesList);
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

    public function confirmDelete($countryId)
    {
        $this->countryToDelete = $countryId;
        $this->showDeleteModal = true;
    }

    public function deleteCountry()
    {
        if ($this->countryToDelete) {
            Country::find($this->countryToDelete)->delete();
            $this->showDeleteModal = false;
            $this->countryToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف الدولة بنجاح.');
            unset($this->countriesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->countriesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Country::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف الدول المحددة بنجاح.');
        unset($this->countriesList);
    }

    // ===== إضافة دولة جديدة =====
    public function saveCountry(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة الدولة بنجاح.');
        unset($this->countriesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editCountry(Country $country): void
    {
        $this->form->setCountry($country);
        $this->dispatch('open-modal');
    }

    // ===== تحديث دولة موجودة =====
    public function updateCountry(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات الدولة بنجاح.');
        unset($this->countriesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitCountry(): void
    {
        if ($this->form->country) {
            $this->updateCountry();
        } else {
            $this->saveCountry();
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
