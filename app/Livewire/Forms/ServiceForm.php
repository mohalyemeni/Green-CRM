<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use App\Enums\DiscountType;
use Livewire\Form;
use App\Models\Service;

class ServiceForm extends Form
{
    public ?Service $service = null;

    // ===== الخصائص (Properties) =====

    // العلاقات
    public ?int    $service_group_id = null;

    // البيانات الأساسية
    public string  $name             = '';
    public ?string $code             = null;
    public ?string $description      = null;
    public ?string $requirements     = null;

    // الحسابات المالية
    public float   $base_cost        = 0;
    public float   $price            = 0;
    public float   $min_price        = 0;
    public float   $max_discount     = 0;
    public string  $discount_type    = DiscountType::AMOUNT->value;

    // الضريبة
    public bool    $is_taxable       = true;
    public float   $tax_rate         = 0;

    // الحالة
    public int     $status           = ActiveStatus::ACTIVE->value;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        return [
            'service_group_id' => 'required|exists:service_groups,id',

            'name'             => 'required|string|max:255',
            'code'             => 'nullable|string|max:100',
            'description'      => 'nullable|string',
            'requirements'     => 'nullable|string',

            'base_cost'        => 'required|numeric|min:0|max:9999999999999.99',
            'price'            => 'required|numeric|min:0|max:9999999999999.99',
            'min_price'        => 'required|numeric|min:0|max:9999999999999.99',
            'max_discount'     => 'required|numeric|min:0|max:9999999999999.99',
            'discount_type'    => 'required|in:amount,percentage',

            'is_taxable'       => 'required|boolean',
            'tax_rate'         => 'required|numeric|min:0|max:100',

            'status'           => 'required|in:0,1',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'service_group_id' => 'مجموعة الخدمة',
            'name'             => 'اسم الخدمة',
            'code'             => 'كود الخدمة',
            'description'      => 'وصف الخدمة',
            'requirements'     => 'متطلبات الخدمة',
            'base_cost'        => 'تكلفة الخدمة',
            'price'            => 'سعر البيع',
            'min_price'        => 'السعر الأدنى',
            'max_discount'     => 'أقصى خصم',
            'discount_type'    => 'نوع الخصم',
            'is_taxable'       => 'خاضعة للضريبة',
            'tax_rate'         => 'نسبة الضريبة',
            'status'           => 'حالة الخدمة',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'service_group_id.required' => 'يجب تحديد مجموعة الخدمة.',
            'service_group_id.exists'   => 'مجموعة الخدمة المحددة غير موجودة في النظام.',
            'name.required'             => 'اسم الخدمة مطلوب.',
            'name.max'                  => 'اسم الخدمة يجب ألا يتجاوز 255 حرفاً.',
            'code.max'                  => 'كود الخدمة يجب ألا يتجاوز 100 حرف.',
            'base_cost.required'        => 'تكلفة الخدمة مطلوبة.',
            'base_cost.numeric'         => 'تكلفة الخدمة يجب أن تكون رقماً.',
            'base_cost.min'             => 'تكلفة الخدمة لا يمكن أن تكون سالبة.',
            'price.required'            => 'سعر البيع مطلوب.',
            'price.numeric'             => 'سعر البيع يجب أن يكون رقماً.',
            'price.min'                 => 'سعر البيع لا يمكن أن يكون سالباً.',
            'min_price.required'        => 'السعر الأدنى مطلوب.',
            'min_price.numeric'         => 'السعر الأدنى يجب أن يكون رقماً.',
            'min_price.min'             => 'السعر الأدنى لا يمكن أن يكون سالباً.',
            'max_discount.required'     => 'أقصى خصم مطلوب.',
            'max_discount.numeric'      => 'أقصى خصم يجب أن يكون رقماً.',
            'max_discount.min'          => 'أقصى خصم لا يمكن أن يكون سالباً.',
            'discount_type.required'    => 'نوع الخصم مطلوب.',
            'discount_type.in'          => 'نوع الخصم يجب أن يكون إما مبلغ أو نسبة.',
            'tax_rate.min'              => 'نسبة الضريبة لا يمكن أن تكون سالبة.',
            'tax_rate.max'              => 'نسبة الضريبة لا يمكن أن تتجاوز 100%.',
            'status.required'           => 'حالة الخدمة مطلوبة.',
        ];
    }

    // ===== تعيين خدمة موجودة للتعديل =====

    public function setService(Service $service): void
    {
        $this->service = $service;

        $this->service_group_id = $service->service_group_id;
        $this->name             = $service->name;
        $this->code             = $service->code;
        $this->description      = $service->description;
        $this->requirements     = $service->requirements;

        $this->base_cost        = (float) $service->base_cost;
        $this->price            = (float) $service->price;
        $this->min_price        = (float) $service->min_price;
        $this->max_discount     = (float) $service->max_discount;
        $this->discount_type    = $service->discount_type instanceof DiscountType
            ? $service->discount_type->value
            : (string) $service->discount_type;

        $this->is_taxable       = (bool) $service->is_taxable;
        $this->tax_rate         = (float) $service->tax_rate;

        $this->status           = $service->status instanceof ActiveStatus
            ? $service->status->value
            : (int) $service->status;
    }

    // ===== تحويل القيم الفارغة إلى Null وتجهيز مصفوفة الحفظ =====

    private function prepareData(): array
    {
        return collect($this->only([
            'service_group_id',
            'name',
            'code',
            'description',
            'requirements',
            'base_cost',
            'price',
            'min_price',
            'max_discount',
            'discount_type',
            'is_taxable',
            'tax_rate',
            'status',
        ]))->map(fn($value) => $value === '' ? null : $value)->toArray();
    }

    // ===== حفظ خدمة جديدة =====

    public function store(): void
    {
        $this->validate();

        $data = $this->prepareData();
        $data['created_by'] = auth()->id();

        // إنشاء كود الخدمة (رقم المجموعة + تسلسل من 4 خانات)
        $groupId = $this->service_group_id;
        $lastCode = Service::where('service_group_id', $groupId)
            ->where('code', 'like', $groupId . '%')
            ->orderByRaw('LENGTH(code) DESC')
            ->orderBy('code', 'desc')
            ->value('code');

        if ($lastCode) {
            $sequence = (int) substr($lastCode, strlen((string)$groupId));
            $nextSequence = $sequence + 1;
        } else {
            $nextSequence = 1;
        }
        $data['code'] = $groupId . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);

        Service::create($data);

        $this->reset();
    }

    // ===== تحديث خدمة موجودة =====

    public function update(): void
    {
        $this->validate();

        $data = $this->prepareData();
        $data['updated_by'] = auth()->id();

        $this->service->update($data);

        $this->reset();
    }
}
