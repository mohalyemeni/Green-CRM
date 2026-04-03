<?php

namespace App\Livewire;

use App\Livewire\Forms\ServiceGroupForm; // فورم مجموعات الخدمات
use App\Models\ServiceGroup;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('مجموعات الخدمات')] class extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';

    // Offcanvas filters (فلاتر متقدمة)
    public $created_from = '';
    public $created_to = '';
    public $selectedStatuses = [];
    public $selectedParents = []; // فلتر جديد: مجموعات تتبع أقسام رئيسية محددة

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected items for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $groupToDelete = null;

    // Form Object
    public ServiceGroupForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'           => ['except' => ''],
        'status'           => ['except' => ''],
        'selectedParents'  => ['except' => []],
        'selectedStatuses' => ['except' => []],
        'created_from'     => ['except' => ''],
        'created_to'       => ['except' => ''],
        'sortField'        => ['except' => 'created_at'],
        'sortDirection'    => ['except' => 'desc'],
    ];

    #[Computed]
    public function serviceGroupsList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب
        $validSortFields = ['name', 'parent_id', 'status', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return ServiceGroup::query()
            // 2. تحسين الأداء: Eager Loading للعلاقات
            ->with(['parent', 'creator', 'editor'])

            // 3. البحث الذكي
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. فلترة الحالة
            ->when($this->status !== '', fn($q) => $q->where('status', (int) $this->status))
            ->when(!empty($this->selectedStatuses), fn($q) => $q->whereIn('status', $this->selectedStatuses))

            // 5. فلترة الأقسام الرئيسية
            ->when(!empty($this->selectedParents), function ($q) {
                $parents = $this->selectedParents;
                $hasMain = in_array('', $parents) || in_array('main', $parents);
                $regularParents = array_filter($parents, fn($p) => $p !== '' && $p !== 'main');

                if ($hasMain && !empty($regularParents)) {
                    $q->where(function ($subQ) use ($regularParents) {
                        $subQ->whereNull('parent_id')
                            ->orWhereIn('parent_id', $regularParents);
                    });
                } elseif ($hasMain) {
                    $q->whereNull('parent_id');
                } elseif (!empty($regularParents)) {
                    $q->whereIn('parent_id', $regularParents);
                }
            })
            // 6. Filter By Dates
            ->when($this->created_from, fn($q) => $q->whereDate('created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('created_at', '<=', $this->created_to))

            // 7. الترتيب
            ->orderBy($sortField, $this->sortDirection)

            // 8. الترقيم
            ->paginate($this->perPage);
    }

    #[Computed]
    public function parentGroups()
    {
        // جلب المجموعات الرئيسية فقط لاستخدامها في الفلاتر
        return ServiceGroup::whereNull('parent_id')
            ->where('status', ActiveStatus::ACTIVE)
            ->pluck('name', 'id');
    }

    #[Computed]
    public function parentGroupsList()
    {
        // نفس البيانات ولكن كمجموعة objects للاستخدام في الـ blade داخل المودال
        return ServiceGroup::whereNull('parent_id')
            ->where('status', ActiveStatus::ACTIVE)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function toggleStatus($groupId)
    {
        $group = ServiceGroup::findOrFail($groupId);
        $group->status = $group->status === ActiveStatus::ACTIVE
            ? ActiveStatus::INACTIVE
            : ActiveStatus::ACTIVE;

        $group->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة المجموعة بنجاح.');
        unset($this->serviceGroupsList);
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
        $this->reset(['search', 'status', 'selectedParents', 'selectedStatuses', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function confirmDelete($groupId)
    {
        $this->groupToDelete = $groupId;
        $this->showDeleteModal = true;
    }

    public function deleteGroup()
    {
        if ($this->groupToDelete) {
            ServiceGroup::find($this->groupToDelete)->delete();
            $this->showDeleteModal = false;
            $this->groupToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف المجموعة بنجاح.');
            unset($this->serviceGroupsList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = collect($this->serviceGroupsList->items())->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        ServiceGroup::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف المجموعات المحددة بنجاح.');
        unset($this->serviceGroupsList);
    }

    public function saveGroup(): void
    {
        $this->form->store();
        $this->dispatch('close-group-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة المجموعة بنجاح.');
        unset($this->serviceGroupsList);
    }

    public function editGroup(ServiceGroup $group): void
    {
        $this->form->setServiceGroup($group);
        // الـ blade يفتح المودال مباشرة باستخدام .then(() => showModal = true)
    }

    public function updateGroup(): void
    {
        $this->form->update();
        $this->dispatch('close-group-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث البيانات بنجاح.');
        unset($this->serviceGroupsList);
    }

    public function submitGroup(): void
    {
        if ($this->form->serviceGroup && $this->form->serviceGroup->exists) {
            $this->updateGroup();
        } else {
            $this->saveGroup();
        }
    }

    public function cancel(): void
    {
        $this->form->reset();
        $this->resetValidation();
    }
};
