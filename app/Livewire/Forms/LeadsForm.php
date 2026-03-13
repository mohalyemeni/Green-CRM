<?php

namespace App\Livewire\Forms;

use App\Models\Lead;
use App\Models\LeadStatus;
use Livewire\Form;
use Illuminate\Validation\Rule;

class LeadsForm extends Form
{
    public ?Lead $lead = null;

    // ===== الخصائص (Properties) =====

    // البيانات الأساسية للهوية
    public string  $lead_number      = '';
    public string  $first_name       = '';
    public ?string $last_name        = null;

    // بيانات التواصل
    public ?string $email            = null;
    public ?string $phone            = null;
    public ?string $mobile           = null;
    public ?string $other_mobile     = null;

    // بيانات العمل
    public ?string $job_title        = null;
    public ?string $company_name     = null;
    public ?string $website          = null;

    // العلاقات (التي قمت بإنشائها مسبقاً)
    public ?int    $company_id       = null;
    public ?int    $branch_id        = null;
    public ?int    $lead_status_id   = null;
    public ?int    $lead_source_id   = null;
    public ?int    $industry_id      = null;
    public ?int    $owner_id         = null;

    // الموقع الجغرافي
    public ?int    $country_id       = null;
    public ?string $state            = null;
    public ?string $city             = null;
    public ?string $address          = null;

    // البيانات المالية والتقييم
    public ?int    $currency_id      = null;
    public float   $estimated_value  = 0;
    public int     $priority         = 2; // Medium
    public int     $rating           = 0;

    // ملاحظات ووصف
    public ?string $description      = null;
    public ?string $notes            = null;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $leadId = $this->lead?->id;

        return [
            'lead_number'     => ['required', 'string', Rule::unique('leads', 'lead_number')->ignore($leadId)],
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'nullable|string|max:100',
            'email'           => ['nullable', 'email', 'max:150', Rule::unique('leads', 'email')->ignore($leadId)],
            'mobile'          => ['nullable', 'string', 'max:50', Rule::unique('leads', 'mobile')->ignore($leadId)],
            'phone'           => 'nullable|string|max:50',

            'company_id'      => 'required|exists:companies,id',
            'branch_id'       => 'nullable|exists:branches,id',
            'lead_status_id'  => 'nullable|exists:lead_statuses,id',
            'lead_source_id'  => 'nullable|exists:lead_sources,id',
            'industry_id'     => 'nullable|exists:industries,id',
            'owner_id'        => 'nullable|exists:users,id',

            'country_id'      => 'nullable|exists:countries,id',
            'currency_id'     => 'nullable|exists:currencies,id',

            'estimated_value' => 'nullable|numeric|min:0',
            'priority'        => 'required|in:1,2,3',
            'rating'          => 'nullable|integer|min:0|max:5',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'lead_number'    => 'رقم العميل المحتمل',
            'first_name'     => 'الاسم الأول',
            'last_name'      => 'اسم العائلة',
            'email'          => 'البريد الإلكتروني',
            'mobile'         => 'رقم الجوال',
            'company_id'     => 'الشركة',
            'lead_status_id' => 'الحالة',
            'lead_source_id' => 'المصدر',
            'estimated_value' => 'القيمة المتوقعة',
        ];
    }

    // ===== تعيين عميل موجود للتعديل =====

    public function setLead(Lead $lead): void
    {
        $this->lead = $lead;

        $this->fill($lead->toArray());

        // التأكد من تحويل القيم المالية والعددية بشكل صحيح
        $this->estimated_value = (float) $lead->estimated_value;
        $this->priority        = (int) $lead->priority;
        $this->rating          = (int) $lead->rating;
    }

    // ===== حفظ عميل جديد =====

    public function store(): void
    {
        // إذا كان رقم العميل فارغاً، نولده تلقائياً (اختياري)
        if (empty($this->lead_number)) {
            $this->lead_number = 'LE-' . date('Ymd') . rand(100, 999);
        }

        // إذا لم يتم اختيار حالة، نأخذ الحالة الافتراضية من جدول الحالات
        if (empty($this->lead_status_id)) {
            $this->lead_status_id = LeadStatus::where('is_default', true)->first()?->id;
        }

        $this->validate();

        Lead::create([
            ...$this->prepareData(),
            'created_by' => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث عميل موجود =====

    public function update(): void
    {
        $this->validate();

        $this->lead->update([
            ...$this->prepareData(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تنظيف البيانات (تحويل النصوص الفارغة إلى Null) =====
    private function prepareData(): array
    {
        return collect($this->all())
            ->except(['lead'])
            ->map(fn($value) => $value === '' ? null : $value)
            ->toArray();
    }
}
