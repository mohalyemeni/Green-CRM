<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Form;
use App\Models\ServiceGroup;

class ServiceGroupForm extends Form
{
    public ?ServiceGroup $serviceGroup = null;

    // ===== الخصائص (Properties) =====

    public string  $name        = '';
    public ?string $description = null;
    public ?int    $parent_id   = null;

    // الحالة
    public int     $status      = ActiveStatus::ACTIVE->value;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'parent_id'   => 'nullable|integer|exists:service_groups,id',
            'status'      => 'required|in:0,1',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'        => 'اسم المجموعة',
            'description' => 'الوصف',
            'parent_id'   => 'المجموعة الأب',
            'status'      => 'حالة المجموعة',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required' => 'يجب إدخال اسم مجموعة الخدمات.',
        ];
    }

    // ===== تعيين مجموعة موجودة للتعديل =====

    public function setServiceGroup(ServiceGroup $serviceGroup): void
    {
        $this->serviceGroup = $serviceGroup;

        $this->name        = $serviceGroup->name;
        $this->description = $serviceGroup->description;
        $this->parent_id   = $serviceGroup->parent_id;

        // التعامل مع حالات الـ Enum أو القيم الرقمية
        $this->status      = $serviceGroup->status instanceof ActiveStatus
            ? $serviceGroup->status->value
            : (int) $serviceGroup->status;
    }

    // ===== تحويل القيم الفارغة إلى Null وتجهيز مصفوفة الحفظ =====

    private function prepareData(): array
    {
        return collect($this->only([
            'name',
            'description',
            'parent_id',
            'status'
        ]))->map(fn($value) => $value === '' ? null : $value)->toArray();
    }

    // ===== حفظ مجموعة جديدة =====

    public function store(): void
    {
        $this->validate();

        $data = $this->prepareData();
        $data['created_by'] = auth()->id();

        ServiceGroup::create($data);

        $this->reset();
    }

    // ===== تحديث مجموعة موجودة =====

    public function update(): void
    {
        $this->validate();

        $data = $this->prepareData();
        $data['updated_by'] = auth()->id();

        $this->serviceGroup->update($data);

        $this->reset();
    }
}
