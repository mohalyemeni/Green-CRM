<?php

namespace App\Livewire\Forms;

use App\Models\Lead;
use App\Models\CrmComment;
use App\Enums\CommentType;
use Livewire\Form;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Validate;

class LeadForm extends Form
{
    public ?Lead $lead = null;

    // --- بيانات العميل ---
    public string $first_name = '';
    public ?string $last_name = null;
    public ?string $phone = null;
    public string $mobile = '';
    public ?string $email = null;

    // --- بيانات العمل والشركة ---
    public ?string $company_name = null;

    // --- العلاقات ---
    public ?int $lead_source_id = null;
    public ?int $assigned_to = null;
    public ?int $lead_status_id = null;

    // --- الأولوية والتصنيف ---
    public int $priority = 2; // 1: Low, 2: Medium, 3: High, 4: Urgent
    
    // --- متغير لحفظ الحالة السابقة للتحقق من الإغلاق ---
    public ?int $previous_status_id = null;

    public function rules(): array
    {
        $leadId = $this->lead?->id;

        return [
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'mobile' => [
                'required',
                'string',
                'max:50',
                Rule::unique('leads', 'mobile')->ignore($leadId)
            ],
            'email' => [
                'nullable',
                'email',
                'max:150',
                Rule::unique('leads', 'email')->ignore($leadId)
            ],
            
            'company_name' => 'nullable|string|max:200',

            'lead_source_id' => 'required|integer|exists:lead_sources,id',
            'assigned_to' => 'nullable|integer|exists:users,id',
            'lead_status_id' => 'nullable|integer|exists:lead_statuses,id',
            'priority' => 'required|integer|in:1,2,3,4',
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'first_name' => 'الاسم الأول',
            'last_name' => 'الاسم الأخير',
            'phone' => 'بطاقة الهاتف',
            'mobile' => 'رقم الجوال',
            'email' => 'البريد الإلكتروني',
            'company_name' => 'اسم الشركة',
            'lead_source_id' => 'المصدر',
            'assigned_to' => 'الموظف المسؤول',
            'lead_status_id' => 'حالة العميل',
            'priority' => 'الأولوية',
        ];
    }

    public function messages(): array
    {
        return [
            'mobile.unique' => 'رقم الجوال مسجل مسبقاً في النظام.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً في النظام.',
        ];
    }

    public function setLead(Lead $lead): void
    {
        $this->lead = $lead;

        $this->first_name = $lead->first_name;
        $this->last_name = $lead->last_name;
        $this->phone = $lead->phone;
        $this->mobile = $lead->mobile;
        $this->email = $lead->email;
        $this->company_name = $lead->company_name;

        $this->lead_source_id = $lead->lead_source_id;
        $this->assigned_to = $lead->assigned_to;
        $this->lead_status_id = $lead->lead_status_id;
        $this->previous_status_id = $lead->lead_status_id;

        $this->priority = $lead->priority;
    }

    public function store(): void
    {
        $this->validate();

        $companyId = \App\Models\Company::first()?->id ?? 1;

        $lead = Lead::create([
            ...collect($this->only([
                'first_name',
                'last_name',
                'phone',
                'mobile',
                'email',
                'company_name',
                'lead_source_id',
                'assigned_to',
                'lead_status_id',
                'priority',
            ]))->map(fn($v) => $v === '' ? null : $v)->toArray(),
            'company_id' => $companyId,
            'created_by' => auth()->id(),
        ]);

        $this->reset();
    }

    public function update(): void
    {
        $this->validate();

        $this->lead->update([
            ...collect($this->only([
                'first_name',
                'last_name',
                'phone',
                'mobile',
                'email',
                'company_name',
                'lead_source_id',
                'assigned_to',
                'lead_status_id',
                'priority',
            ]))->map(fn($v) => $v === '' ? null : $v)->toArray(),
            'updated_by' => auth()->id(),
        ]);

        if ($this->lead_status_id && $this->lead_status_id != $this->previous_status_id) {
            $newStatus = \App\Models\LeadStatus::find($this->lead_status_id);
            if ($newStatus && $newStatus->is_closed) {
                $owner = auth()->user()?->full_name ?? 'System';
                $this->addComment("تم تغيير حالة العميل إلى [{$newStatus->name}] وإغلاق الملف بواسطة {$owner}", CommentType::CLOSED);
            }
        }

        $this->reset();
    }

    /**
     * تنفيذ عملية الإغلاق وإضافة تعليق توثيقي
     */
    public function closeLead(string $reasonDetails): void
    {
        $this->addComment($reasonDetails, CommentType::CLOSED);
    }

    /**
     * دالة لإضافة تعليقات للعميل المحتمل
     */
    public function addComment(string $body, CommentType $type = CommentType::NOTE): void
    {
        if ($this->lead) {
            $this->lead->comments()->create([
                'body' => $body,
                'type' => $type->value,
                'user_id' => auth()->id(),
                'created_by' => auth()->id(),
            ]);
        }
    }
}
