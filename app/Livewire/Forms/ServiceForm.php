<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Service;

class ServicesForm extends Form
{
    public ?Service $service = null;

    // ===== الخصائص (Properties) =====

    // العلاقات
    public ?int    $service_group_id   = null;

    // البيانات الأساسية
    public string  $name               = '';
    public ?string $description        = null;
    public ?string $requirements       = null;

    // الحسابات المالية
    public float   $price              = 0;
    public float   $cost               = 0;
    public bool    $taxable            = true;

    // الحالة
    public int     $status             = ActiveStatus::ACTIVE->value;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        return [
            'service_group_id' => 'required|exists:service_groups,id',

            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'requirements'     => 'nullable|string',

            'price'            => 'required|numeric|min:0|max:9999999999999.99',
            'cost'             => 'required|numeric|min:0|max:9999999999999.99',
            'taxable'          => 'required|boolean',

            'status'           => 'required|in:0,1',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'service_group_id' => 'مجموعة الخدمة',
            'name'             => 'اسم الخدمة',
            'description'      => 'وصف الخدمة',
            'requirements'     => 'متطلبات الخدمة',
            'price'            => 'سعر البيع',
            'cost'             => 'تكلفة الخدمة',
            'taxable'          => 'خاضعة للضريبة',
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
            'price.required'            => 'سعر البيع مطلوب.',
            'price.numeric'             => 'سعر البيع يجب أن يكون رقماً.',
            'price.min'                 => 'سعر البيع لا يمكن أن يكون سالباً.',
            'cost.required'             => 'تكلفة الخدمة مطلوبة.',
            'cost.numeric'              => 'تكلفة الخدمة يجب أن تكون رقماً.',
            'cost.min'                  => 'تكلفة الخدمة لا يمكن أن تكون سالبة.',
            'taxable.required'          => 'يجب تحديد ما إذا كانت الخدمة خاضعة للضريبة.',
            'status.required'           => 'حالة الخدمة مطلوبة.',
        ];
    }

    // ===== تعيين خدمة موجودة للتعديل =====

    public function setService(Service $service): void
    {
        $this->service = $service;

        $this->service_group_id = $service->service_group_id;

        $this->name             = $service->name;
        $this->description      = $service->description;
        $this->requirements     = $service->requirements;

        $this->price            = (float) $service->price;
        $this->cost             = (float) $service->cost;
        $this->taxable          = (bool) $service->taxable;

        $this->status           = isset($service->status->value) ? $service->status->value : $service->status;
    }

    // ===== تحويل القيم الفارغة إلى Null وتجهيز مصفوفة الحفظ =====

    private function prepareData(): array
    {
        return collect($this->only([
            'service_group_id',
            'name',
            'description',
            'requirements',
            'price',
            'cost',
            'taxable',
            'status'
        ]))->map(fn($value) => $value === '' ? null : $value)->toArray();
    }

    // ===== حفظ خدمة جديدة =====

    public function store(): void
    {
        $this->validate();

        $data = $this->prepareData();
        $data['created_by'] = auth()->id();

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
