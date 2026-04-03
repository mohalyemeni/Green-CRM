<?php

namespace App\Livewire;

use App\Livewire\Forms\ServiceForm;
use App\Models\Service;
use App\Models\ServiceGroup;
use App\Enums\ActiveStatus;
use App\Enums\DiscountType;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('الخدمات')] class extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';

    // Offcanvas filters (فلاتر متقدمة)
    public $created_from        = '';
    public $created_to          = '';
    public $selectedStatuses    = [];
    public $selectedGroups      = [];   // فلتر المجموعات
    public $selectedDiscounts   = [];   // فلتر نوع الخصم

    public $sortField     = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected items for bulk actions
    public $selectedIds = [];
    public $selectAll   = false;

    // Modal state
    public $showDeleteModal = false;
    public $serviceToDelete = null;

    // Form Object
    public ServiceForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'           => ['except' => ''],
        'status'           => ['except' => ''],
        'selectedGroups'   => ['except' => []],
        'selectedStatuses' => ['except' => []],
        'selectedDiscounts' => ['except' => []],
        'created_from'     => ['except' => ''],
        'created_to'       => ['except' => ''],
        'sortField'        => ['except' => 'created_at'],
        'sortDirection'    => ['except' => 'desc'],
    ];

    #[Computed]
    public function servicesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب
        $validSortFields = ['name', 'code', 'price', 'base_cost', 'status', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Service::query()
            // 2. تحسين الأداء: Eager Loading للعلاقات
            ->with(['serviceGroup', 'creator', 'editor'])

            // 3. البحث الذكي
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. فلترة الحالة (شريط الأدوات)
            ->when($this->status !== '', fn($q) => $q->where('status', (int) $this->status))

            // 5. فلترة الحالة (Offcanvas)
            ->when(!empty($this->selectedStatuses), fn($q) => $q->whereIn('status', $this->selectedStatuses))

            // 6. فلترة المجموعات
            ->when(!empty($this->selectedGroups), fn($q) => $q->whereIn('service_group_id', $this->selectedGroups))

            // 7. فلترة نوع الخصم
            ->when(!empty($this->selectedDiscounts), fn($q) => $q->whereIn('discount_type', $this->selectedDiscounts))

            // 8. فلترة التواريخ
            ->when($this->created_from, fn($q) => $q->whereDate('created_at', '>=', $this->created_from))
            ->when($this->created_to,   fn($q) => $q->whereDate('created_at', '<=', $this->created_to))

            // 9. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 10. الترقيم
            ->paginate($this->perPage);
    }

    #[Computed]
    public function activeServiceGroups()
    {
        // جلب المجموعات النشطة لاستخدامها في المودال والفلاتر
        return ServiceGroup::where('status', ActiveStatus::ACTIVE)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    #[Computed]
    public function discountTypes()
    {
        return DiscountType::cases();
    }

    public function toggleStatus($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->status = $service->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $service->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة الخدمة بنجاح.');
        unset($this->servicesList);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
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
        $this->reset(['search', 'status', 'selectedGroups', 'selectedStatuses', 'selectedDiscounts', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($serviceId)
    {
        $this->serviceToDelete = $serviceId;
        $this->showDeleteModal = true;
    }

    public function deleteService()
    {
        if ($this->serviceToDelete) {
            Service::find($this->serviceToDelete)->delete();
            $this->showDeleteModal = false;
            $this->serviceToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف الخدمة بنجاح.');
            unset($this->servicesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = collect($this->servicesList->items())->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Service::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll   = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف الخدمات المحددة بنجاح.');
        unset($this->servicesList);
    }

    public function saveService(): void
    {
        $this->form->store();
        $this->dispatch('close-service-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة الخدمة بنجاح.');
        unset($this->servicesList);
    }

    public function editService(Service $service): void
    {
        $this->form->setService($service);
    }

    public function updateService(): void
    {
        $this->form->update();
        $this->dispatch('close-service-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات الخدمة بنجاح.');
        unset($this->servicesList);
    }

    public function submitService(): void
    {
        if ($this->form->service && $this->form->service->exists) {
            $this->updateService();
        } else {
            $this->saveService();
        }
    }

    public function cancel(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }
};
