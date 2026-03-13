<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\LeadSource;

class LeadSourcesForm extends Form
{
    public ?LeadSource $leadSource = null;

    // ===== الخصائص (Properties) =====

    public string  $name        = '';
    public ?string $name_en     = null;
    public ?string $code        = null;
    public ?string $description = null;

    public ?string $color       = '#405189'; // لون افتراضي (مثلاً لون نظام Velzon الأساسي)
    public ?string $icon        = null;

    public int     $status      = ActiveStatus::ACTIVE->value;
    public int     $sort_order  = 0;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $sourceId = $this->leadSource?->id;

        return [
            'name'        => 'required|string|min:2|max:150',
            'name_en'     => 'nullable|string|max:150',
            'code'        => 'nullable|string|max:50|unique:lead_sources,code,' . $sourceId,
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'icon'        => 'nullable|string|max:100',
            'status'      => 'required|in:0,1',
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'        => 'اسم المصدر بالعربية',
            'name_en'     => 'اسم المصدر بالإنجليزية',
            'code'        => 'كود المصدر',
            'description' => 'الوصف',
            'color'       => 'اللون',
            'icon'        => 'الأيقونة',
            'status'      => 'الحالة',
            'sort_order'  => 'ترتيب العرض',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required' => 'اسم المصدر مطلوب.',
            'name.min'      => 'اسم المصدر يجب أن يكون حرفين على الأقل.',
            'code.unique'   => 'هذا الكود مستخدم من قبل مصدر آخر.',
            'status.required' => 'حالة المصدر مطلوبة.',
        ];
    }

    // ===== تعيين مصدر موجود للتعديل =====

    public function setLeadSource(LeadSource $leadSource): void
    {
        $this->leadSource  = $leadSource;

        $this->name        = $leadSource->name;
        $this->name_en     = $leadSource->name_en;
        $this->code        = $leadSource->code;
        $this->description = $leadSource->description;
        $this->color       = $leadSource->color;
        $this->icon        = $leadSource->icon;
        $this->sort_order  = $leadSource->sort_order;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status      = isset($leadSource->status->value) ? $leadSource->status->value : $leadSource->status;
    }

    // ===== حفظ مصدر جديد =====

    public function store(): void
    {
        $this->validate();

        LeadSource::create([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'color'       => $this->color ?: null,
            'icon'        => $this->icon ?: null,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'created_by'  => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث مصدر موجود =====

    public function update(): void
    {
        $this->validate();

        $this->leadSource->update([
            'name'        => $this->name ?: null,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'color'       => $this->color ?: null,
            'icon'        => $this->icon ?: null,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'updated_by'  => auth()->id(),
        ]);

        $this->reset();
    }
}
