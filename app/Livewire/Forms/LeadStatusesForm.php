<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\LeadStatus;

class LeadStatusesForm extends Form
{
    public ?LeadStatus $leadStatus = null;

    // ===== الخصائص (Properties) =====

    public string  $name        = '';
    public ?string $name_en     = null;
    public ?string $code        = null;
    public ?string $description = null;
    public ?string $color       = '#405189'; // لون افتراضي متناسق مع النظام

    public bool    $is_default  = false; // هل هي الحالة الافتراضية؟
    public bool    $is_closed   = false; // هل تعني إغلاق الطلب؟

    public int     $status      = ActiveStatus::ACTIVE->value;
    public int     $sort_order  = 0;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $statusId = $this->leadStatus?->id;

        return [
            'name'        => 'required|string|min:2|max:150',
            'name_en'     => 'nullable|string|max:150',
            'code'        => 'nullable|string|max:50|unique:lead_statuses,code,' . $statusId,
            'description' => 'nullable|string|max:500',
            'color'       => 'nullable|string|max:20',
            'is_default'  => 'boolean',
            'is_closed'   => 'boolean',
            'status'      => 'required|in:0,1',
            'sort_order'  => 'nullable|integer|min:0',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'        => 'اسم الحالة بالعربية',
            'name_en'     => 'اسم الحالة بالإنجليزية',
            'code'        => 'كود الحالة',
            'description' => 'الوصف',
            'color'       => 'اللون',
            'is_default'  => 'الحالة الافتراضية',
            'is_closed'   => 'حالة إغلاق',
            'status'      => 'الحالة',
            'sort_order'  => 'ترتيب العرض',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required' => 'اسم الحالة مطلوب.',
            'code.unique'   => 'هذا الكود مستخدم من قبل حالة أخرى.',
        ];
    }

    // ===== تعيين حالة موجودة للتعديل =====

    public function setLeadStatus(LeadStatus $leadStatus): void
    {
        $this->leadStatus = $leadStatus;

        $this->name        = $leadStatus->name;
        $this->name_en     = $leadStatus->name_en;
        $this->code        = $leadStatus->code;
        $this->description = $leadStatus->description;
        $this->color       = $leadStatus->color;
        $this->is_default  = (bool) $leadStatus->is_default;
        $this->is_closed   = (bool) $leadStatus->is_closed;
        $this->sort_order  = $leadStatus->sort_order;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status      = isset($leadStatus->status->value) ? $leadStatus->status->value : $leadStatus->status;
    }

    // ===== حفظ حالة جديدة =====

    public function store(): void
    {
        $this->validate();

        // منطق "حالة افتراضية واحدة فقط": إذا تم اختيار هذه الحالة كافتراضية، نقوم بإلغاء الافتراضية عن البقية
        if ($this->is_default) {
            LeadStatus::where('is_default', true)->update(['is_default' => false]);
        }

        LeadStatus::create([
            'name'        => $this->name,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'color'       => $this->color ?: null,
            'is_default'  => $this->is_default,
            'is_closed'   => $this->is_closed,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'created_by'  => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث حالة موجودة =====

    public function update(): void
    {
        $this->validate();

        if ($this->is_default) {
            LeadStatus::where('id', '!=', $this->leadStatus->id)
                ->where('is_default', true)
                ->update(['is_default' => false]);
        }

        $this->leadStatus->update([
            'name'        => $this->name,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'color'       => $this->color ?: null,
            'is_default'  => $this->is_default,
            'is_closed'   => $this->is_closed,
            'sort_order'  => (int) $this->sort_order,
            'status'      => $this->status,
            'updated_by'  => auth()->id(),
        ]);

        $this->reset();
    }
}
