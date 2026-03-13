<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Branch;

class BranchesForm extends Form
{
    public ?Branch $branch = null;

    // ===== الخصائص (Properties) =====

    // العلاقات
    public ?int    $company_id          = null;
    public ?int    $country_id          = null;
    public ?int    $currency_id         = null;

    // البيانات الأساسية
    public ?string $code                = null;
    public string  $name                = '';
    public ?string $name_en             = null;
    public string  $slug                = '';

    // البيانات القانونية والضريبية
    public ?string $commercial_register = null;
    public ?string $tax_number          = null;

    // بيانات العنوان والموقع
    public ?string $state               = null;
    public ?string $city                = null;
    public ?string $district            = null;
    public ?string $building_number     = null;
    public ?string $street_address      = null;
    public ?string $postal_code         = null;
    public ?string $po_box              = null;

    // الإعدادات والتواصل
    public string  $timezone            = 'Asia/Riyadh'; // القيمة الافتراضية
    public ?string $phone               = null;
    public ?string $mobile              = null;
    public ?string $email               = null;
    public ?string $fax                 = null;
    public $logo                        = null;

    // الحالة
    public int     $status              = ActiveStatus::ACTIVE->value;

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $branchId = $this->branch?->id;

        return [
            'company_id'          => 'required|exists:companies,id',
            'country_id'          => 'nullable|exists:countries,id',
            'currency_id'         => 'nullable|exists:currencies,id',

            'code'                => 'nullable|string|max:255|unique:branches,code,' . ($this->branch ? $this->branch->id : 'NULL'),
            'name'                => 'required|string|max:255',
            'name_en'             => 'nullable|string|max:255',
            'slug'                => 'nullable|string|max:255|unique:branches,slug,' . ($this->branch ? $this->branch->id : 'NULL'),

            'commercial_register' => 'nullable|string|max:100',
            'tax_number'          => 'nullable|string|max:100',

            'state'               => 'nullable|string|max:150',
            'city'                => 'nullable|string|max:150',
            'district'            => 'nullable|string|max:150',
            'building_number'     => 'nullable|string|max:50',
            'street_address'      => 'nullable|string|max:255',
            'postal_code'         => 'nullable|string|max:20',
            'po_box'              => 'nullable|string|max:50',

            'timezone'            => 'required|string|max:50',
            'phone'               => 'nullable|string|max:50',
            'mobile'              => 'nullable|string|max:50',
            'email'               => 'nullable|email|max:255',
            'fax'                 => 'nullable|string|max:50',
            'logo'                => 'nullable|string|max:255',

            'status'              => 'required|in:0,1',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'company_id'          => 'الشركة التابع لها',
            'code'                => 'كود الفرع',
            'name'                => 'اسم الفرع',
            'name_en'             => 'اسم الفرع الأجنبي',
            'slug'                => 'الرابط المخصص',
            'commercial_register' => 'السجل التجاري',
            'tax_number'          => 'الرقم الضريبي',
            'country_id'          => 'الدولة',
            'state'               => 'الولاية/المحافظة',
            'city'                => 'المدينة',
            'district'            => 'الحي',
            'building_number'     => 'رقم المبنى',
            'street_address'      => 'العنوان التفصيلي',
            'postal_code'         => 'الرمز البريدي',
            'po_box'              => 'صندوق البريد',
            'timezone'            => 'المنطقة الزمنية',
            'currency_id'         => 'العملة الافتراضية للفرع',
            'phone'               => 'رقم الهاتف الأرضي',
            'mobile'              => 'رقم الجوال',
            'email'               => 'البريد الإلكتروني',
            'fax'                 => 'رقم الفاكس',
            'logo'                => 'شعار الفرع',
            'status'              => 'حالة الفرع',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'company_id.required' => 'يجب تحديد الشركة التي يتبع لها هذا الفرع.',
            'company_id.exists'   => 'الشركة المحددة غير موجودة في النظام.',
            'name.required'       => 'اسم الفرع مطلوب.',
            'slug.required'       => 'الرابط المخصص مطلوب لتوليد مسار الفرع.',
            'slug.unique'         => 'هذا الرابط مستخدم من قبل فرع آخر.',
            'code.unique'         => 'كود الفرع هذا مستخدم مسبقاً، الرجاء اختيار كود آخر.',
            'email.email'         => 'صيغة البريد الإلكتروني غير صحيحة.',
            'timezone.required'   => 'المنطقة الزمنية مطلوبة لضبط أوقات الفواتير.',
        ];
    }

    // ===== تعيين فرع موجود للتعديل =====

    public function setBranch(Branch $branch): void
    {
        $this->branch = $branch;

        $this->company_id          = $branch->company_id;
        $this->country_id          = $branch->country_id;
        $this->currency_id         = $branch->currency_id;

        $this->code                = $branch->code;
        $this->name                = $branch->name;
        $this->name_en             = $branch->name_en;
        $this->slug                = $branch->slug;

        $this->commercial_register = $branch->commercial_register;
        $this->tax_number          = $branch->tax_number;

        $this->state               = $branch->state;
        $this->city                = $branch->city;
        $this->district            = $branch->district;
        $this->building_number     = $branch->building_number;
        $this->street_address      = $branch->street_address;
        $this->postal_code         = $branch->postal_code;
        $this->po_box              = $branch->po_box;

        $this->timezone            = $branch->timezone ?? 'Asia/Riyadh';
        $this->phone               = $branch->phone;
        $this->mobile              = $branch->mobile;
        $this->email               = $branch->email;
        $this->fax                 = $branch->fax;
        $this->logo                = $branch->logo;

        $this->status              = isset($branch->status->value) ? $branch->status->value : $branch->status;
    }

    // ===== تحويل القيم الفارغة إلى Null وتجهيز مصفوفة الحفظ =====
    // هذه الدالة المساعدة (Helper) تجعل كود الحفظ والتعديل نظيفاً ومختصراً جداً
    private function prepareData(): array
    {
        return collect($this->only([
            'company_id',
            'country_id',
            'currency_id',
            // 'code', // Managed by model
            'name',
            'name_en',
            // 'slug', // Managed by model
            'commercial_register',
            'tax_number',
            'state',
            'city',
            'district',
            'building_number',
            'street_address',
            'postal_code',
            'po_box',
            'timezone',
            'phone',
            'mobile',
            'email',
            'fax',
            'logo',
            'status'
        ]))->map(fn($value) => $value === '' ? null : $value)->toArray();
    }

    // ===== حفظ فرع جديد =====

    public function store(): void
    {
        $this->validate();

        $data = $this->prepareData();
        $data['created_by'] = auth()->id();

        Branch::create($data);

        $this->reset();
    }

    // ===== تحديث فرع موجود =====

    public function update(): void
    {
        $this->validate();

        $data = $this->prepareData();
        $data['updated_by'] = auth()->id();

        $this->branch->update($data);

        $this->reset();
    }
}
