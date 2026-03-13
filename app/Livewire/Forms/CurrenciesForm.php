<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use App\Models\Currency;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CurrenciesForm extends Form
{
    public ?Currency $currency = null;

    // ===== الخصائص (Properties) =====

    public string  $name            = '';
    public ?string $code            = null;
    public ?string $symbol          = null;
    public ?string $fraction_name   = null;

    public ?float  $exchange_rate   = 1;
    public ?float  $equivalent      = 1;
    public ?float  $max_exchange_rate = 0;
    public ?float  $min_exchange_rate = 0;

    public bool    $is_local        = false;
    public bool    $is_inventory    = false;
    public int     $status          = ActiveStatus::ACTIVE->value; // 1 = ActiveStatus::ACTIVE

    public ?string $notes           = null;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $currencyId = $this->currency?->id;

        return [
            'name'              => 'required|string|min:2|max:100',
            'code'              => 'required|string|max:10|unique:currencies,code,' . $currencyId,
            'symbol'            => 'nullable|string|max:10',
            'fraction_name'     => 'nullable|string|max:50',

            'exchange_rate'     => 'required|numeric|min:0.000001',
            'equivalent'        => 'nullable|numeric|min:0',
            'max_exchange_rate' => 'nullable|numeric|min:0',
            'min_exchange_rate' => 'nullable|numeric|min:0',

            'is_local'          => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $exists = Currency::where('is_local', true)
                            ->when($this->currency?->id, fn($q) => $q->where('id', '!=', $this->currency->id))
                            ->exists();
                        if ($exists) {
                            $fail('هناك عملة أخرى مسجلة بالفعل كعملة محلية. يجب إزالة الخاصية من العملة الأخرى أولاً.');
                        }
                    }
                }
            ],
            'is_inventory'      => [
                'boolean',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        $exists = Currency::where('is_inventory', true)
                            ->when($this->currency?->id, fn($q) => $q->where('id', '!=', $this->currency->id))
                            ->exists();
                        if ($exists) {
                            $fail('هناك عملة أخرى مسجلة بالفعل كعملة مخزون. يجب إزالة الخاصية من العملة الأخرى أولاً.');
                        }
                    }
                }
            ],
            'status'            => 'required|in:0,1',

            'notes'             => 'nullable|string',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'              => 'اسم العملة',
            'code'              => 'كود العملة',
            'symbol'            => 'الرمز',
            'fraction_name'     => 'اسم الكسر',
            'exchange_rate'     => 'سعر الصرف',
            'equivalent'        => 'المعادل',
            'max_exchange_rate' => 'أعلى سعر صرف',
            'min_exchange_rate' => 'أدنى سعر صرف',
            'is_local'          => 'عملة محلية',
            'is_inventory'      => 'عملة المخزون',
            'status'            => 'الحالة',
            'notes'             => 'ملاحظات',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required'             => 'اسم العملة مطلوب.',
            'name.min'                  => 'اسم العملة يجب أن يكون حرفين على الأقل.',
            'name.max'                  => 'اسم العملة لا يتجاوز 100 حرف.',
            'code.required'             => 'كود العملة مطلوب.',
            'code.unique'               => 'كود العملة مسجل مسبقاً.',
            'code.max'                  => 'كود العملة لا يتجاوز 10 أحرف.',
            'exchange_rate.required'    => 'سعر الصرف مطلوب.',
            'exchange_rate.min'         => 'سعر الصرف يجب أن يكون أكبر من الصفر.',
            'status.required'           => 'حالة العملة مطلوبة.',
            'status.in'                 => 'قيمة الحالة غير صحيحة.',
        ];
    }

    // ===== تعيين عملة موجودة للتعديل =====

    public function setCurrency(Currency $currency): void
    {
        $this->currency          = $currency;

        $this->name              = $currency->name;
        $this->code              = $currency->code;
        $this->symbol            = $currency->symbol;
        $this->fraction_name     = $currency->fraction_name;

        $this->exchange_rate     = (float) $currency->exchange_rate ?? 0;
        $this->equivalent        = (float) $currency->equivalent ?? 0;
        $this->max_exchange_rate = (float) $currency->max_exchange_rate ?? 0;
        $this->min_exchange_rate = (float) $currency->min_exchange_rate ?? 0;

        $this->is_local          = (bool) $currency->is_local;
        $this->is_inventory      = (bool) $currency->is_inventory;

        // Handling Enum or integer value based on model casting
        $this->status            = isset($currency->status->value) ? $currency->status->value : $currency->status;

        $this->notes             = $currency->notes;
    }

    // ===== حفظ عملة جديدة =====

    public function store(): void
    {
        $this->validate();

        Currency::create([
            ...collect($this->only([
                'name',
                'code',
                'symbol',
                'fraction_name',
                'exchange_rate',
                'equivalent',
                'max_exchange_rate',
                'min_exchange_rate',
                'is_local',
                'is_inventory',
                'status',
                'notes',
            ]))->map(fn($v) => $v === '' ? null : $v)->toArray(),
            'created_by' => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث عملة موجودة =====

    public function update(): void
    {
        $this->validate();

        $this->currency->update([
            ...collect($this->only([
                'name',
                'code',
                'symbol',
                'fraction_name',
                'exchange_rate',
                'equivalent',
                'max_exchange_rate',
                'min_exchange_rate',
                'is_local',
                'is_inventory',
                'status',
                'notes',
            ]))->map(fn($v) => $v === '' ? null : $v)->toArray(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset();
    }
}
