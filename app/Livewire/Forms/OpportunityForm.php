<?php

namespace App\Livewire\Forms;

use App\Models\Opportunity;
use App\Models\PipelineStage;
use App\Models\CrmComment;
use App\Enums\CommentType;
use Livewire\Form;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class OpportunityForm extends Form
{
    public ?Opportunity $opportunity = null;

    // ===== البيانات الأساسية =====
    public string  $title              = '';
    public ?string $description        = null;
    public string  $opportunity_number = '';

    // ===== العلاقات =====
    public ?int    $company_id            = null;
    public ?int    $branch_id             = null;
    public ?int    $customer_id           = null;
    public ?int    $pipeline_id           = null;
    public ?int    $stage_id              = null;
    public ?int    $opportunity_source_id = null;
    public ?string $opportunity_type      = null;

    // ===== البيانات المالية =====
    public ?int   $currency_id       = null;
    public float  $expected_revenue  = 0;
    public int    $probability       = 0;

    // ===== التوقيت والأولوية =====
    public ?string $expected_close_date = null;
    public string  $priority            = 'medium';

    // ===== الإغلاق والخسارة =====
    public ?int    $lost_reason_id    = null;
    public ?string $lost_reason_notes = null;

    // ===== التعيين =====
    public ?int $assigned_to = null;

    // ===== متغير للتحقق من تغيير المرحلة =====
    public ?int    $previous_stage_id = null;
    public ?string $previous_priority = null;

    // ===== قواعد التحقق =====
    public function rules(): array
    {
        $oppId = $this->opportunity?->id;

        $rules = [
            'title'                 => 'required|string|max:255',
            'opportunity_number'    => [
                'required', 'string', 'max:50',
                Rule::unique('opportunities', 'opportunity_number')->ignore($oppId),
            ],
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

        if ($this->stage_id) {
            $stage = PipelineStage::find($this->stage_id);
            if ($stage && $stage->is_lost) {
                $rules['lost_reason_id'] = 'required_without:lost_reason_notes|exists:lost_reasons,id';
                $rules['lost_reason_notes'] = 'required_without:lost_reason_id|string';
            }
        }

        return $rules;
    }

    // ===== أسماء الحقول =====
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
            'lost_reason_id'     => 'سبب الخسارة',
            'lost_reason_notes'  => 'ملاحظات الخسارة',
        ];
    }

    // ===== رسائل التحقق المخصصة =====
    public function messages(): array
    {
        return [
            'opportunity_number.unique'          => 'الرقم المرجعي مسجل مسبقاً في النظام.',
            'customer_id.required'               => 'يجب تحديد العميل المرتبط بالفرصة.',
            'title.required'                     => 'عنوان الفرصة مطلوب.',
            'lost_reason_id.required_without'    => 'عفواً، يجب تحديد سبب الخسارة أو كتابة ملاحظات الخسارة عند تغيير المرحلة إلى خسارة.',
            'lost_reason_notes.required_without' => 'عفواً، يجب تحديد سبب الخسارة أو كتابة ملاحظات الخسارة عند تغيير المرحلة إلى خسارة.',
        ];
    }

    // ===== تعيين فرصة موجودة للتعديل =====
    public function setOpportunity(Opportunity $opportunity): void
    {
        $this->opportunity = $opportunity;

        $this->title              = $opportunity->title;
        $this->description        = $opportunity->description;
        $this->opportunity_number = $opportunity->opportunity_number;
        $this->company_id         = $opportunity->company_id;
        $this->branch_id          = $opportunity->branch_id;
        $this->customer_id        = $opportunity->customer_id;
        $this->pipeline_id        = $opportunity->pipeline_id;
        $this->stage_id           = $opportunity->stage_id;
        $this->opportunity_source_id = $opportunity->opportunity_source_id;
        $this->opportunity_type   = $opportunity->opportunity_type;
        $this->currency_id        = $opportunity->currency_id;
        $this->expected_revenue   = (float) $opportunity->expected_revenue;
        $this->probability        = (int) $opportunity->probability;
        $this->expected_close_date = $opportunity->expected_close_date
            ? Carbon::parse($opportunity->expected_close_date)->format('Y-m-d')
            : null;
        $this->priority           = $opportunity->priority ?? 'medium';
        $this->lost_reason_id     = $opportunity->lost_reason_id;
        $this->lost_reason_notes  = $opportunity->lost_reason_notes;
        $this->assigned_to        = $opportunity->assigned_to;

        // حفظ الحالة السابقة للتحقق من تغيير المرحلة
        $this->previous_stage_id  = $opportunity->stage_id;
        $this->previous_priority  = $opportunity->priority;
    }

    // ===== حفظ فرصة جديدة =====
    public function store(): Opportunity
    {
        // توليد الرقم المرجعي تلقائياً
        if (empty($this->opportunity_number)) {
            $latestOpp = Opportunity::withTrashed()->latest('id')->first();
            $nextId = $latestOpp ? $latestOpp->id + 1 : 1;
            $this->opportunity_number = 'OPP-' . date('Y') . str_pad($nextId, 5, '0', STR_PAD_LEFT);
        }

        // جلب الاحتمالية تلقائياً من المرحلة إذا لم تُحدد
        if (!empty($this->stage_id) && $this->probability == 0) {
            $this->probability = PipelineStage::find($this->stage_id)?->probability ?? 0;
        }

        // الشركة الافتراضية
        if (empty($this->company_id)) {
            $this->company_id = \App\Models\Company::first()?->id ?? 1;
        }

        $this->validate();

        $opportunity = Opportunity::create([
            ...$this->prepareData(),
            'created_by' => auth()->id(),
        ]);

        $this->reset();

        return $opportunity;
    }

    // ===== تحديث فرصة موجودة =====
    public function update(): void
    {
        $this->validate();

        // جلب الاحتمالية من المرحلة عند تغييرها
        if ($this->stage_id && $this->stage_id !== $this->previous_stage_id) {
            $stage = PipelineStage::find($this->stage_id);
            if ($stage) {
                $this->probability = (int) $stage->probability;

                // تحقق من إغلاق الصفقة (Won أو Lost)
                if ($stage->is_won) {
                    $this->opportunity->update([
                        'closed_at' => now(),
                        ...$this->prepareData(),
                        'updated_by' => auth()->id(),
                    ]);
                    $owner = auth()->user()?->full_name ?? 'النظام';
                    $this->addComment(
                        "تم إغلاق الفرصة بنجاح (WON) على مرحلة [{$stage->name}] بواسطة {$owner} بتاريخ " . now()->format('Y-m-d H:i'),
                        CommentType::CLOSED
                    );
                    $this->reset(['previous_stage_id', 'previous_priority']);
                    return;
                }

                if ($stage->is_lost) {
                    $this->opportunity->update([
                        'closed_at' => now(),
                        ...$this->prepareData(),
                        'updated_by' => auth()->id(),
                    ]);
                    $owner = auth()->user()?->full_name ?? 'النظام';
                    $reason = $this->opportunity->lostReason?->name ?? 'غير محدد';
                    $this->addComment(
                        "تم إغلاق الفرصة كخسارة (LOST) سبب: [{$reason}] على مرحلة [{$stage->name}] بواسطة {$owner} بتاريخ " . now()->format('Y-m-d H:i'),
                        CommentType::CLOSED
                    );
                    $this->reset(['previous_stage_id', 'previous_priority']);
                    return;
                }
            }
        }

        $this->opportunity->update([
            ...$this->prepareData(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset(['previous_stage_id', 'previous_priority']);
    }

    // ===== إضافة تعليق =====
    public function addComment(string $body, CommentType $type = CommentType::NOTE): void
    {
        if ($this->opportunity) {
            $this->opportunity->comments()->create([
                'body'       => $body,
                'type'       => $type->value,
                'user_id'    => auth()->id(),
                'created_by' => auth()->id(),
            ]);
        }
    }

    // ===== تنظيف البيانات قبل الحفظ =====
    private function prepareData(): array
    {
        return collect($this->all())
            ->except(['opportunity', 'previous_stage_id', 'previous_priority'])
            ->map(fn($value) => $value === '' ? null : $value)
            ->toArray();
    }
}
