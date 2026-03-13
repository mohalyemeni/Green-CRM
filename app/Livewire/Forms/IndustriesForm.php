<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Industry; // التأكد من استخدام مودل القطاعات

class IndustriesForm extends Form
{
    public ?Industry $industry = null;

    // ===== الخصائص (Properties) =====

    public string  $name        = '';
    public ?string $name_en     = null;
    public ?string $description = null;
    public ?string $icon        = null; // كلاس الأيقونة أو مسار الصورة

    public int     $status      = ActiveStatus::ACTIVE->value; // الافتراضي مفعل
    public int     $sort_order  = 0; // الترتيب الافتراضي

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        // تم إزالة قيد الـ unique من الاسم لتسهيل الإدارة، ولكن يمكن إضافته إذا رغبت
        return [
            'name'        => 'required|string|min:2|max:150',
            'name_en'     => 'nullable|string|max:150',
            'description' => 'nullable|string|max:500',

            // الأيقونة عادة تكون نصاً قصيراً مثل "ri-hospital-line"
            'icon'        => 'nullable|string|max:100',

            'status'      => 'required|in:0,1',
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'        => 'اسم القطاع (بالعربية)',
            'name_en'     => 'اسم القطاع (بالإنجليزية)',
            'description' => 'وصف القطاع',
            'icon'        => 'الأيقونة',
            'status'      => 'الحالة',
            'sort_order'  => 'ترتيب العرض',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required'       => 'اسم القطاع مطلوب.',
            'name.min'            => 'اسم القطاع يجب أن يكون حرفين على الأقل.',
            'status.required'     => 'حالة القطاع مطلوبة.',
            'status.in'           => 'قيمة الحالة غير صحيحة.',
            'sort_order.integer'  => 'يجب أن يكون ترتيب العرض رقماً صحيحاً.',
        ];
    }

    // ===== تعيين قطاع موجود للتعديل =====

    public function setIndustry(Industry $industry): void
    {
        $this->industry     = $industry;

        $this->name         = $industry->name;
        $this->name_en      = $industry->name_en;
        $this->description  = $industry->description;
        $this->icon         = $industry->icon;
        $this->sort_order   = $industry->sort_order;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status       = isset($industry->status->value) ? $industry->status->value : $industry->status;
    }

    // ===== حفظ قطاع جديد =====

    public function store(): void
    {
        $this->validate();

        Industry::create([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'description' => $this->description ?: null,
            'icon'        => $this->icon ?: null,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'created_by'  => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث قطاع موجود =====

    public function update(): void
    {
        $this->validate();

        $this->industry->update([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'description' => $this->description ?: null,
            'icon'        => $this->icon ?: null,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'updated_by'  => auth()->id(),
        ]);

        $this->reset();
    }
}
