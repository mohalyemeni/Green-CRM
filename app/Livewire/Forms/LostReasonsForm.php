<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\LostReason;

class LostReasonsForm extends Form
{
    public ?LostReason $lostReason = null;

    // ===== الخصائص (Properties) =====

    public string  $name        = '';
    public ?string $name_en     = null;
    public ?string $code        = null;
    public ?string $description = null;

    public int     $status      = ActiveStatus::ACTIVE->value; // 1 = ActiveStatus::ACTIVE
    public int     $sort_order  = 0;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $reasonId = $this->lostReason?->id;

        return [
            'name'        => 'required|string|min:2|max:150',
            'name_en'     => 'nullable|string|max:150',
            'code'        => 'nullable|string|max:50|unique:lost_reasons,code,' . $reasonId,
            'description' => 'nullable|string|max:500',
            'status'      => 'required|in:0,1',
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'        => 'اسم السبب (بالعربية)',
            'name_en'     => 'اسم السبب (بالإنجليزية)',
            'code'        => 'كود السبب',
            'description' => 'الوصف',
            'status'      => 'الحالة',
            'sort_order'  => 'ترتيب العرض',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required' => 'اسم سبب الخسارة مطلوب.',
            'name.min'      => 'اسم السبب يجب أن يكون حرفين على الأقل.',
            'code.unique'   => 'هذا الكود مسجل مسبقاً لسبب آخر.',
            'status.required' => 'حالة السبب مطلوبة.',
            'status.in'      => 'قيمة الحالة غير صحيحة.',
        ];
    }

    // ===== تعيين سبب موجود للتعديل =====

    public function setLostReason(LostReason $lostReason): void
    {
        $this->lostReason  = $lostReason;

        $this->name        = $lostReason->name;
        $this->name_en     = $lostReason->name_en;
        $this->code        = $lostReason->code;
        $this->description = $lostReason->description;
        $this->sort_order  = $lostReason->sort_order;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status      = isset($lostReason->status->value) ? $lostReason->status->value : $lostReason->status;
    }

    // ===== حفظ سبب جديد =====

    public function store(): void
    {
        $this->validate();

        LostReason::create([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'created_by'  => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث سبب موجود =====

    public function update(): void
    {
        $this->validate();

        $this->lostReason->update([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'updated_by'  => auth()->id(),
        ]);

        $this->reset();
    }
}
