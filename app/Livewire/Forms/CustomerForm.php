<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use Livewire\Attributes\Validate;
use Livewire\Form;

class CustomerForm extends Form
{
    public ?Customer $customer = null;

    // ===== الخصائص (Properties) =====

    // البيانات الأساسية
    #[Validate]
    public string  $name            = '';
    public ?string $national_id     = null;
    public ?int    $age             = null;
    public ?int    $gender          = null;
    public int     $status          = 1;

    // بيانات التواصل
    public ?string $mobile          = null;
    public ?string $email           = null;

    // بيانات العنوان
    public ?string $general_address = null;
    public ?string $building_number = null;
    public ?string $street_name     = null;
    public ?string $district        = null;
    public ?string $city            = null;
    public ?string $country         = null;

    // البيانات المالية
    public ?string $tax_number      = null;
    public ?string $dealing_method  = null;
    public float   $credit_limit    = 0;

    // الملاحظات
    public ?string $notes           = null;


    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $customerId = $this->customer?->id;

        return [
            // البيانات الأساسية
            'name'            => 'required|string|min:2|max:255',
            'national_id'     => 'nullable|string|max:20|unique:customers,national_id,' . $customerId,
            'age'             => 'nullable|integer|min:1|max:120',
            'gender'          => 'nullable|in:1,2',
            'status'          => 'required|integer|in:1,2,3',

            // بيانات التواصل
            'mobile'          => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:255|unique:customers,email,' . $customerId,

            // بيانات العنوان
            'general_address' => 'nullable|string|max:500',
            'building_number' => 'nullable|string|max:50',
            'street_name'     => 'nullable|string|max:255',
            'district'        => 'nullable|string|max:255',
            'city'            => 'nullable|string|max:255',
            'country'         => 'nullable|string|max:255',

            // البيانات المالية
            'tax_number'      => 'nullable|string|max:50',
            'dealing_method'  => 'nullable|in:cash,credit',
            'credit_limit'    => 'nullable|numeric|min:0',

            // الملاحظات
            'notes'           => 'nullable|string|max:1000',
        ];
    }


    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'            => 'اسم العميل',
            'national_id'     => 'الهوية الوطنية',
            'age'             => 'العمر',
            'gender'          => 'الجنس',
            'status'          => 'الحالة',
            'mobile'          => 'رقم الجوال',
            'email'           => 'البريد الإلكتروني',
            'general_address' => 'العنوان العام',
            'building_number' => 'رقم المبنى',
            'street_name'     => 'اسم الشارع',
            'district'        => 'الحي',
            'city'            => 'المدينة',
            'country'         => 'الدولة',
            'tax_number'      => 'الرقم الضريبي',
            'dealing_method'  => 'طريقة التعامل',
            'credit_limit'    => 'حد الدين',
            'notes'           => 'الملاحظات',
        ];
    }


    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required'        => 'اسم العميل مطلوب.',
            'name.min'             => 'الاسم يجب أن يكون على الأقل حرفين.',
            'name.max'             => 'الاسم لا يتجاوز 255 حرفاً.',
            'national_id.unique'   => 'رقم الهوية الوطنية مسجل مسبقاً.',
            'national_id.max'      => 'رقم الهوية لا يتجاوز 20 رقماً.',
            'age.integer'          => 'العمر يجب أن يكون رقماً صحيحاً.',
            'age.min'              => 'العمر يجب أن يكون 1 على الأقل.',
            'age.max'              => 'العمر لا يتجاوز 120 سنة.',
            'gender.in'            => 'قيمة الجنس غير صحيحة.',
            'status.required'      => 'حالة العميل مطلوبة.',
            'status.in'            => 'قيمة الحالة غير صحيحة.',
            'mobile.max'           => 'رقم الجوال لا يتجاوز 20 رقماً.',
            'email.email'          => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique'         => 'البريد الإلكتروني مسجل مسبقاً.',
            'dealing_method.in'    => 'طريقة التعامل يجب أن تكون كاش أو آجل.',
            'credit_limit.numeric' => 'حد الدين يجب أن يكون رقماً.',
            'credit_limit.min'     => 'حد الدين لا يمكن أن يكون سالباً.',
            'notes.max'            => 'الملاحظات لا تتجاوز 1000 حرف.',
        ];
    }


    // ===== تعيين عميل موجود للتعديل =====

    public function setCustomer(Customer $customer): void
    {
        $this->customer        = $customer;

        // البيانات الأساسية
        $this->name            = $customer->name;
        $this->national_id     = $customer->national_id;
        $this->age             = $customer->age;
        $this->gender          = $customer->gender?->value;
        $this->status          = $customer->status->value;

        // بيانات التواصل
        $this->mobile          = $customer->mobile;
        $this->email           = $customer->email;

        // بيانات العنوان
        $this->general_address = $customer->general_address;
        $this->building_number = $customer->building_number;
        $this->street_name     = $customer->street_name;
        $this->district        = $customer->district;
        $this->city            = $customer->city;
        $this->country         = $customer->country;

        // البيانات المالية
        $this->tax_number      = $customer->tax_number;
        $this->dealing_method  = $customer->dealing_method;
        $this->credit_limit    = (float) $customer->credit_limit;

        // الملاحظات
        $this->notes           = $customer->notes;
    }


    // ===== حفظ عميل جديد =====

    public function store(): void
    {
        $this->validate();

        // توليد رقم عميل تلقائي
        $lastId         = Customer::max('id') ?? 0;
        $customerNumber = 'CRM-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

        Customer::create(attributes: [
            'customer_number' => $customerNumber,
            ...collect($this->only([
                'name',
                'national_id',
                'age',
                'gender',
                'status',
                'mobile',
                'email',
                'general_address',
                'building_number',
                'street_name',
                'district',
                'city',
                'country',
                'tax_number',
                'dealing_method',
                'credit_limit',
                'notes',
            ]))->map(fn($v) => $v === '' ? null : $v)->toArray(),
            'created_by' => auth()->id(),
        ]);

        $this->reset();
    }


    // ===== تحديث عميل موجود =====

    public function update(): void
    {
        $this->validate();

        $this->customer->update(attributes: [
            ...collect($this->only([
                'name',
                'national_id',
                'age',
                'gender',
                'status',
                'mobile',
                'email',
                'general_address',
                'building_number',
                'street_name',
                'district',
                'city',
                'country',
                'tax_number',
                'dealing_method',
                'credit_limit',
                'notes',
            ]))->map(fn($v) => $v === '' ? null : $v)->toArray(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset();
    }
}
