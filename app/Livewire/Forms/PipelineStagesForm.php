<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\PipelineStage;

class PipelineStagesForm extends Form
{
    public ?PipelineStage $pipelineStage = null;

    // ===== الخصائص (Properties) =====

    public string  $name         = '';
    public ?string $name_en      = null;
    public ?string $code         = null;
    public ?string $description  = null;

    public float   $probability  = 0; // احتمالية الفوز (%)
    public int     $sort_order   = 0;
    public ?string $color        = '#405189'; // لون افتراضي للمرحلة

    public bool    $is_won       = false; // هل المرحلة تعني فوز؟
    public bool    $is_lost      = false; // هل المرحلة تعني خسارة؟

    public int     $status       = ActiveStatus::ACTIVE->value;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $stageId = $this->pipelineStage?->id;

        return [
            'name'        => 'required|string|min:2|max:150',
            'name_en'     => 'nullable|string|max:150',
            'code'        => 'nullable|string|max:50|unique:pipeline_stages,code,' . $stageId,
            'description' => 'nullable|string|max:500',

            'probability' => 'required|numeric|min:0|max:100',
            'sort_order'  => 'required|integer|min:0',
            'color'       => 'nullable|string|max:20',

            'is_won'      => 'boolean',
            'is_lost'     => 'boolean',

            'status'      => 'required|in:0,1',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'        => 'اسم المرحلة',
            'name_en'     => 'اسم المرحلة بالإنجليزية',
            'code'        => 'كود المرحلة',
            'description' => 'الوصف',
            'probability' => 'احتمالية الفوز',
            'sort_order'  => 'ترتيب العرض',
            'color'       => 'اللون المميز',
            'status'      => 'الحالة',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required'        => 'اسم المرحلة مطلوب.',
            'probability.required' => 'يجب تحديد نسبة احتمالية الفوز.',
            'probability.max'      => 'الاحتمالية لا يمكن أن تتجاوز 100%.',
            'code.unique'          => 'كود المرحلة مستخدم مسبقاً.',
        ];
    }

    // ===== تعيين مرحلة موجودة للتعديل =====

    public function setStage(PipelineStage $stage): void
    {
        $this->pipelineStage = $stage;

        $this->name        = $stage->name;
        $this->name_en     = $stage->name_en;
        $this->code        = $stage->code;
        $this->description = $stage->description;
        $this->probability = (float) $stage->probability;
        $this->sort_order  = $stage->sort_order;
        $this->color       = $stage->color;
        $this->is_won      = (bool) $stage->is_won;
        $this->is_lost     = (bool) $stage->is_lost;

        $this->status      = isset($stage->status->value) ? $stage->status->value : $stage->status;
    }

    // ===== حفظ مرحلة جديدة =====

    public function store(): void
    {
        $this->validate();

        PipelineStage::create([
            'name'        => $this->name,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'probability' => $this->probability,
            'sort_order'  => $this->sort_order,
            'color'       => $this->color ?: null,
            'is_won'      => $this->is_won,
            'is_lost'     => $this->is_lost,
            'status'      => $this->status,
            'created_by'  => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث مرحلة موجودة =====

    public function update(): void
    {
        $this->validate();

        $this->pipelineStage->update([
            'name'        => $this->name,
            'name_en'     => $this->name_en ?: null,
            'code'        => $this->code ? strtoupper($this->code) : null,
            'description' => $this->description ?: null,
            'probability' => $this->probability,
            'sort_order'  => $this->sort_order,
            'color'       => $this->color ?: null,
            'is_won'      => $this->is_won,
            'is_lost'     => $this->is_lost,
            'status'      => $this->status,
            'updated_by'  => auth()->id(),
        ]);

        $this->reset();
    }
}
