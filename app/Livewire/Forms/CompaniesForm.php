<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Company;

class CompaniesForm extends Form
{
    public ?Company $company = null;

    // ===== الخصائص (Properties) =====

    public string  $name             = '';
    public ?string $name_en          = null;
    public ?string $short_name       = null;
    public string  $slug             = '';
    public ?string $website          = null;
    public $logo                     = null; // جعلناه بدون نوع محدد لدعم رفع الملفات (UploadedFile) لاحقاً

    public ?int    $base_currency_id = null;

    public int     $status           = ActiveStatus::ACTIVE->value; // 1 = ActiveStatus::ACTIVE
    public ?string $notes            = null;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $companyId = $this->company?->id;

        return [
            'name'             => 'required|string|min:2|max:255',
            'name_en'          => 'nullable|string|max:255',
            'short_name'       => 'nullable|string|max:50',

            // الـ slug يجب أن يكون فريداً على مستوى جدول الشركات
            'slug'             => 'required|string|max:255|unique:companies,slug,' . $companyId,

            'website'          => 'nullable|url|max:255',
            'logo'             => 'nullable|string|max:255', // إذا كنت تستخدم رفع ملفات غيرها إلى: nullable|image|max:2048

            // التحقق من أن العملة موجودة فعلياً في جدول العملات
            'base_currency_id' => 'required|exists:currencies,id',

            'status'           => 'required|in:0,1',
            'notes'            => 'nullable|string',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'             => 'اسم الشركة',
            'name_en'          => 'اسم الشركة الأجنبي',
            'short_name'       => 'الاسم المختصر',
            'slug'             => 'رابط النظام (Slug)',
            'website'          => 'الموقع الإلكتروني',
            'logo'             => 'شعار الشركة',
            'base_currency_id' => 'العملة الأساسية',
            'status'           => 'الحالة',
            'notes'            => 'ملاحظات',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required'             => 'اسم الشركة مطلوب.',
            'name.min'                  => 'اسم الشركة يجب أن يكون حرفين على الأقل.',
            'slug.required'             => 'رابط النظام مطلوب لتوليد مسار الشركة.',
            'slug.unique'               => 'هذا الرابط مستخدم من قبل شركة أخرى، الرجاء اختيار رابط مختلف.',
            'base_currency_id.required' => 'تحديد العملة الأساسية للنشاط التجاري أمر إلزامي.',
            'base_currency_id.exists'   => 'العملة المحددة غير صالحة أو غير موجودة بالنظام.',
            'website.url'               => 'صيغة الموقع الإلكتروني غير صحيحة.',
            'status.required'           => 'حالة الشركة مطلوبة.',
            'status.in'                 => 'قيمة الحالة غير صحيحة.',
        ];
    }

    // ===== تعيين شركة موجودة للتعديل =====

    public function setCompany(Company $company): void
    {
        $this->company          = $company;

        $this->name             = $company->name;
        $this->name_en          = $company->name_en;
        $this->short_name       = $company->short_name;
        $this->slug             = $company->slug;
        $this->website          = $company->website;
        $this->logo             = $company->logo;
        $this->base_currency_id = $company->base_currency_id;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status           = isset($company->status->value) ? $company->status->value : $company->status;

        $this->notes            = $company->notes;
    }

    // ===== حفظ شركة جديدة =====

    public function store(): void
    {
        $this->validate();

        Company::create([
            'name'             => $this->name ?: null,
            'name_en'          => $this->name_en ?: null,
            'short_name'       => $this->short_name ?: null,
            'slug'             => $this->slug ?: null,
            'website'          => $this->website ?: null,
            'logo'             => $this->logo ?: null,
            'base_currency_id' => $this->base_currency_id ?: null,
            'status'           => $this->status,
            'notes'            => $this->notes ?: null,
            'created_by'       => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث شركة موجودة =====

    public function update(): void
    {
        $this->validate();

        $this->company->update([
            'name'             => $this->name ?: null,
            'name_en'          => $this->name_en ?: null,
            'short_name'       => $this->short_name ?: null,
            'slug'             => $this->slug ?: null,
            'website'          => $this->website ?: null,
            'logo'             => $this->logo ?: null,
            'base_currency_id' => $this->base_currency_id ?: null,
            'status'           => $this->status,
            'notes'            => $this->notes ?: null,
            'updated_by'       => auth()->id(),
        ]);

        $this->reset();
    }
}
