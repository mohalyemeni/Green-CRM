<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\CustomerGroup; // التأكد من استدعاء المودل الصحيح

class CustomerGroupForm extends Form
{
    public ?CustomerGroup $customerGroup = null;

    // ===== الخصائص (Properties) =====

    public string  $name        = '';
    public ?string $name_en     = null;
    public ?string $code        = null;
    public ?string $description = null;

    public int     $status      = ActiveStatus::ACTIVE->value; // 1 = ActiveStatus::ACTIVE

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $groupId = $this->customerGroup?->id;

        return [
            'name'        => 'required|string|min:2|max:150',
            'name_en'     => 'nullable|string|max:150',

            // كود المجموعة يتم توليده تلقائياً إذا تركه المستخدم فارغاً
            'code'        => 'nullable|string|max:50|unique:customer_groups,code,' . $groupId,

            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:0,1',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'        => 'اسم المجموعة',
            'name_en'     => 'اسم المجموعة الأجنبي',
            'code'        => 'كود المجموعة',
            'description' => 'الوصف',
            'status'      => 'الحالة',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المجموعة مطلوب.',
            'name.min'      => 'اسم المجموعة يجب أن يكون حرفين على الأقل.',
            'code.unique'   => 'هذا الكود مستخدم من قبل مجموعة أخرى، الرجاء اختيار كود مختلف.',
            'status.required' => 'حالة المجموعة مطلوبة.',
            'status.in'     => 'قيمة الحالة غير صحيحة.',
        ];
    }

    // ===== تعيين مجموعة موجودة للتعديل =====

    public function setCustomerGroup(CustomerGroup $customerGroup): void
    {
        $this->customerGroup = $customerGroup;

        $this->name          = $customerGroup->name;
        $this->name_en       = $customerGroup->name_en;
        $this->code          = $customerGroup->code;
        $this->description   = $customerGroup->description;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status        = isset($customerGroup->status->value) ? $customerGroup->status->value : $customerGroup->status;
    }

    // ===== حفظ مجموعة جديدة =====

    public function store(): void
    {
        $this->validate();

        CustomerGroup::create([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'status'      => $this->status,
            'created_by'  => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث مجموعة موجودة =====

    public function update(): void
    {
        $this->validate();

        $this->customerGroup->update([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'status'      => $this->status,
            'updated_by'  => auth()->id(),
        ]);

        $this->reset();
    }
}
