<?php

use App\Livewire\Forms\CurrenciesForm;
use App\Models\Currency;
use App\Enums\ActiveStatus;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Computed;
use Livewire\WithoutUrlPagination;

new #[Title('بيانات العملات')] class extends Component
{
    use WithPagination, WithFileUploads, WithoutUrlPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters
    public $search = '';
    public $status = '';  // For generic simple filter if any

    // Offcanvas filters
    public $created_from = '';
    public $created_to = '';
    public $selectedStatuses = [];
    public $isLocal = false;
    public $isInventory = false;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    public $perPage = 10;

    // Selected currencies for bulk actions
    public $selectedIds = [];
    public $selectAll = false;

    // Modal state
    public $showDeleteModal = false;
    public $currencyToDelete = null;

    // Form Object للتحقق والحفظ والتعديل
    public CurrenciesForm $form;

    // Query string for URL persistence
    protected $queryString = [
        'search'        => ['except' => ''],
        'status'        => ['except' => ''],
        'isLocal'       => ['except' => false],
        'isInventory'   => ['except' => false],
        'selectedStatuses' => ['except' => []],
        'created_from'  => ['except' => ''],
        'created_to'    => ['except' => ''],
        'sortField'     => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    #[Computed]
    public function currenciesList()
    {
        // 1. حماية أمنية: تحديد الحقول المسموح بالترتيب بناءً عليها
        $validSortFields = ['code', 'name', 'symbol', 'exchange_rate', 'created_at'];
        $sortField = in_array($this->sortField, $validSortFields) ? $this->sortField : 'created_at';

        return Currency::query()
            // 2. تحسين الأداء: جلب بيانات المستخدمين المرتبطين دفعة واحدة (Eager Loading)
            ->with(['creator', 'editor'])

            // 3. البحث الذكي (يحتوي على ترتيب داخلي حسب الأهمية)
            ->when($this->search, fn($q) => $q->search('%' . $this->search . '%'))

            // 4. معالجة الحالة العامة وحالة الاستخدام
            ->when($this->status !== '', function ($q) {
                return $q->where('status', $this->status);
            })
            ->when(!empty($this->selectedStatuses), function ($q) {
                return $q->whereIn('status', $this->selectedStatuses);
            })
            ->when($this->isLocal, function ($q) {
                return $q->where('is_local', true);
            })
            ->when($this->isInventory, function ($q) {
                return $q->where('is_inventory', true);
            })

            // 5. Filter By Dates (Offcanvas)
            ->when($this->created_from, fn($q) => $q->whereDate('currencies.created_at', '>=', $this->created_from))
            ->when($this->created_to, fn($q) => $q->whereDate('currencies.created_at', '<=', $this->created_to))

            // 6. الترتيب: يُطبَّق دائماً (مع الحماية الأمنية)
            ->orderBy($sortField, $this->sortDirection)

            // 7. الترقيم
            ->paginate($this->perPage);
    }

    public function toggleStatus($currencyId)
    {
        $currency = Currency::findOrFail($currencyId);
        $currency->status = $currency->status === \App\Enums\ActiveStatus::ACTIVE
            ? \App\Enums\ActiveStatus::INACTIVE
            : \App\Enums\ActiveStatus::ACTIVE;

        $currency->save();
        $this->dispatch('notify', type: 'info', message: 'تم تغيير حالة العملة بنجاح.');
        unset($this->currenciesList);
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
        $this->reset(['search', 'status', 'isLocal', 'isInventory', 'selectedStatuses', 'created_from', 'created_to']);
        $this->resetPage();
        $this->dispatch('filters-reset');
    }

    public function resetAndCloseFilters()
    {
        $this->resetFilters();
        $this->dispatch('close-offcanvas');
    }

    public function confirmDelete($currencyId)
    {
        $this->currencyToDelete = $currencyId;
        $this->showDeleteModal = true;
    }

    public function deleteCurrency()
    {
        if ($this->currencyToDelete) {
            Currency::find($this->currencyToDelete)->delete();
            $this->showDeleteModal = false;
            $this->currencyToDelete = null;
            $this->dispatch('notify', type: 'warning', message: 'تم حذف العملة بنجاح.');
            unset($this->currenciesList);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedIds = $this->currenciesList->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedIds = [];
        }
    }

    public function deleteMultiple()
    {
        Currency::whereIn('id', $this->selectedIds)->delete();
        $this->selectedIds = [];
        $this->selectAll = false;
        $this->dispatch('notify', type: 'warning', message: 'تم حذف العملات المحددة بنجاح.');
        unset($this->currenciesList);
    }

    // ===== إضافة عملة جديدة =====
    public function saveCurrency(): void
    {
        $this->form->store();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'success', message: 'تم إضافة العملة بنجاح.');
        unset($this->currenciesList);
    }

    // ===== تحضير فورم التعديل =====
    public function editCurrency(Currency $currency): void
    {
        $this->form->setCurrency($currency);
        $this->dispatch('open-modal');
    }

    // ===== تحديث عملة موجودة =====
    public function updateCurrency(): void
    {
        $this->form->update();
        $this->dispatch('close-modal');
        $this->dispatch('notify', type: 'info', message: 'تم تحديث بيانات العملة بنجاح.');
        unset($this->currenciesList);
    }

    // ===== دالة موحّدة (إضافة أو تعديل) — مستخدَمة في wire:submit =====
    public function submitCurrency(): void
    {
        if ($this->form->currency) {
            $this->updateCurrency();
        } else {
            $this->saveCurrency();
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
