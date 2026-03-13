<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\OpportunitySource; // التأكد من استدعاء الموديل الصحيح

class OpportunitySourcesForm extends Form
{
    public ?OpportunitySource $opportunitySource = null;

    // ===== الخصائص (Properties) =====

    public string  $name        = '';
    public ?string $name_en     = null;
    public ?string $code        = null;
    public ?string $description = null;

    public ?string $color       = '#405189'; // اللون الافتراضي
    public ?string $icon        = null;

    public int     $status      = ActiveStatus::ACTIVE->value;
    public int     $sort_order  = 0;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $sourceId = $this->opportunitySource?->id;

        return [
            'name'        => 'required|string|min:2|max:150',
            'name_en'     => 'nullable|string|max:150',
            'code'        => 'nullable|string|max:50|unique:opportunity_sources,code,' . $sourceId,
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
            'name.required' => 'اسم مصدر الفرصة مطلوب.',
            'name.min'      => 'اسم المصدر يجب أن يكون حرفين على الأقل.',
            'code.unique'   => 'هذا الكود مستخدم من قبل مصدر فرص آخر.',
            'status.required' => 'حالة المصدر مطلوبة.',
        ];
    }

    // ===== تعيين مصدر موجود للتعديل =====

    public function setOpportunitySource(OpportunitySource $opportunitySource): void
    {
        $this->opportunitySource = $opportunitySource;

        $this->name        = $opportunitySource->name;
        $this->name_en     = $opportunitySource->name_en;
        $this->code        = $opportunitySource->code;
        $this->description = $opportunitySource->description;
        $this->color       = $opportunitySource->color;
        $this->icon        = $opportunitySource->icon;
        $this->sort_order  = $opportunitySource->sort_order;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status      = isset($opportunitySource->status->value) ? $opportunitySource->status->value : $opportunitySource->status;
    }

    // ===== حفظ مصدر جديد =====

    public function store(): void
    {
        $this->validate();

        OpportunitySource::create([
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

        $this->opportunitySource->update([
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
