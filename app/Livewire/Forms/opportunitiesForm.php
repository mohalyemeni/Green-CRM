<?php

namespace App\Livewire\Forms;

use App\Models\Opportunity;
use App\Models\PipelineStage;
use Livewire\Form;
use Illuminate\Validation\Rule;

class OpportunitiesForm extends Form
{
    public ?Opportunity $opportunity = null;

    // ===== الخصائص (Properties) =====

    // البيانات الأساسية
    public string  $title                 = '';
    public ?string $description           = null;
    public string  $opportunity_number    = '';

    // العلاقات (الروابط)
    public ?int    $company_id            = null;
    public ?int    $branch_id             = null;
    public ?int    $customer_id           = null;
    public ?int    $pipeline_id           = null;
    public ?int    $stage_id              = null;
    public ?int    $opportunity_source_id = null;
    public ?string $opportunity_type      = null;

    // البيانات المالية والتقييم
    public ?int    $currency_id           = null;
    public float   $expected_revenue      = 0;
    public int     $probability           = 0;

    // التوقيت والحالة
    public ?string $expected_close_date   = null;
    public string  $priority              = 'medium'; // low, medium, high, urgent

    // الإغلاق والخسارة
    public ?int    $lost_reason_id        = null;
    public ?string $lost_reason_notes     = null;

    // التعيين (المسؤول)
    public ?int    $assigned_to           = null;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $oppId = $this->opportunity?->id;

        return [
            'title'                 => 'required|string|max:255',
            'opportunity_number'    => ['required', 'string', 'max:50', Rule::unique('opportunities', 'opportunity_number')->ignore($oppId)],
            'description'           => 'nullable|string',

            'company_id'            => 'required|exists:companies,id',
            'branch_id'             => 'nullable|exists:branches,id',
            'customer_id'           => 'required|exists:customers,id',

            'pipeline_id'           => 'nullable|exists:pipelines,id',
            'stage_id'              => 'nullable|exists:pipeline_stages,id',
            'opportunity_source_id' => 'nullable|exists:opportunity_sources,id',
            'opportunity_type'      => 'nullable|string|max:100',

            'currency_id'           => 'nullable|exists:currencies,id',
            'expected_revenue'      => 'nullable|numeric|min:0',
            'probability'           => 'nullable|integer|min:0|max:100',

            'expected_close_date'   => 'nullable|date',
            'priority'              => 'required|in:low,medium,high,urgent',

            'lost_reason_id'        => 'nullable|exists:lost_reasons,id',
            'lost_reason_notes'     => 'nullable|string',

            'assigned_to'           => 'nullable|exists:users,id',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'title'              => 'عنوان الفرصة',
            'opportunity_number' => 'الرقم المرجعي',
            'customer_id'        => 'العميل',
            'company_id'         => 'الشركة',
            'priority'           => 'الأولوية',
            'expected_revenue'   => 'الإيراد المتوقع',
            'probability'        => 'الاحتمالية',
        ];
    }

    // ===== تعيين فرصة موجودة للتعديل =====

    public function setOpportunity(Opportunity $opportunity): void
    {
        $this->opportunity = $opportunity;

        $this->fill($opportunity->toArray());

        // ضبط الصيغ
        $this->expected_revenue = (float) $opportunity->expected_revenue;
        $this->probability      = (int) $opportunity->probability;

        // جلب التاريخ بصيغة مناسبة لحقل الـ input type="date"
        $this->expected_close_date = $opportunity->expected_close_date ? \Carbon\Carbon::parse($opportunity->expected_close_date)->format('Y-m-d') : null;
    }

    // ===== حفظ فرصة جديدة =====

    public function store(): void
    {
        // توليد رقم تلقائي للفرصة
        if (empty($this->opportunity_number)) {
            $this->opportunity_number = 'OPP-' . date('Ymd') . '-' . rand(100, 999);
        }

        // جلب الاحتمالية تلقائياً إذا تم اختيار مرحلة وكانت الاحتمالية 0
        if (!empty($this->stage_id) && $this->probability == 0) {
            $this->probability = PipelineStage::find($this->stage_id)?->probability ?? 0;
        }

        $this->validate();

        Opportunity::create([
            ...$this->prepareData(),
            'created_by' => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث فرصة موجودة =====

    public function update(): void
    {
        $this->validate();

        $this->opportunity->update([
            ...$this->prepareData(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تنظيف البيانات =====
    private function prepareData(): array
    {
        return collect($this->all())
            ->except(['opportunity'])
            ->map(fn($value) => $value === '' ? null : $value)
            ->toArray();
    }
}
