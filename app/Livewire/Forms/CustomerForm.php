<?php

namespace App\Livewire\Forms;

use App\Models\Customer;
use App\Enums\CustomerStatus;
use App\Enums\Gender;
use Livewire\Attributes\Validate;
use Livewire\Form;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class CustomerForm extends Form
{
    public ?Customer $customer = null;

    // ===== الخصائص (Properties) =====

    // البيانات الأساسية
    public ?string $customer_number = null;
    #[Validate]
    public string $name = '';
    public ?int $gender = null;

    // بيانات التواصل
    public ?string $phone = null;
    public string $mobile = '';
    public ?string $whatsapp = null;
    public ?string $email = null;

    // بيانات العنوان
    public ?string $address = null;
    public ?string $building_number = null;
    public ?string $street_name = null;
    public ?string $district = null;
    public ?string $city = null;
    public ?int $country_id = null;

    // الحالة والملاحظات (القيمة الافتراضية من Enum)
    public int $status = CustomerStatus::ACTIVE->value;
    public ?string $notes = null;

    // ===== قواعد التحقق (Validation Rules) =====
    public function rules(): array
    {
        $customerId = $this->customer?->id;

        return [
            // البيانات الأساسية
            'customer_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('customers', 'customer_number')->ignore($customerId),
            ],
            'name' => 'required|string|min:2|max:255',
            'gender' => ['nullable', 'integer', new Enum(Gender::class)],

            // بيانات التواصل
            'phone' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:255',
            'whatsapp' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customerId),
            ],

            // بيانات العنوان
            'address' => 'nullable|string|max:1000',
            'building_number' => 'nullable|string|max:255',
            'street_name' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'country_id' => 'nullable|integer|exists:countries,id',

            // الحالة والملاحظات
            'status' => ['required', 'integer', new Enum(CustomerStatus::class)],
            'notes' => 'nullable|string|max:2000',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====
    public function validationAttributes(): array
    {
        return [
            'customer_number' => 'رقم العميل',
            'name' => 'اسم العميل',
            'gender' => 'الجنس',
            'phone' => 'رقم الهاتف',
            'mobile' => 'رقم الموبايل',
            'whatsapp' => 'رقم الوتس',
            'email' => 'البريد الإلكتروني',
            'address' => 'العنوان العام',
            'building_number' => 'رقم المبنى',
            'street_name' => 'اسم الشارع',
            'district' => 'الحي',
            'city' => 'المدينة',
            'country_id' => 'الدولة',
            'status' => 'الحالة',
            'notes' => 'الملاحظات',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====
    public function messages(): array
    {
        return [
            'customer_number.unique' => 'رقم العميل مسجل مسبقاً.',
            'name.required' => 'اسم العميل مطلوب.',
            'name.min' => 'الاسم يجب أن يكون على الأقل حرفين.',
            'name.max' => 'الاسم لا يتجاوز 255 حرفاً.',
            'gender.Illuminate\Validation\Rules\Enum' => 'قيمة الجنس غير صحيحة.',
            'mobile.required' => 'رقم الموبايل مطلوب.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً.',
            'status.required' => 'حالة العميل مطلوبة.',
            'status.Illuminate\Validation\Rules\Enum' => 'قيمة الحالة غير صحيحة.',
        ];
    }

    // ===== تعيين عميل موجود للتعديل =====
    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;

        // البيانات الأساسية
        $this->customer_number = $customer->customer_number;
        $this->name = $customer->name;

        // معالجة Gender Enum
        $this->gender = $customer->gender instanceof Gender ? $customer->gender->value : ($customer->getRawOriginal('gender') ? (int) $customer->getRawOriginal('gender') : null);

        // بيانات التواصل
        $this->phone = $customer->phone;
        $this->mobile = $customer->mobile;
        $this->whatsapp = $customer->whatsapp;
        $this->email = $customer->email;

        // بيانات العنوان
        $this->address = $customer->address;
        $this->building_number = $customer->building_number;
        $this->street_name = $customer->street_name;
        $this->district = $customer->district;
        $this->city = $customer->city;
        $this->country_id = $customer->country_id;

        // الحالة والملاحظات
        // معالجة CustomerStatus Enum
        $this->status = $customer->status instanceof CustomerStatus ? $customer->status->value : (int) $customer->getRawOriginal('status');
        $this->notes = $customer->notes;
    }

    // ===== حفظ عميل جديد =====
    public function store(): void
    {
        $this->validate();

        // توليد رقم عميل تلقائي إذا لم يتم إدخاله
        if (empty($this->customer_number)) {
            $lastId = Customer::max('id') ?? 0;
            $this->customer_number = 'CUST-' . str_pad($lastId + 1, 3, '0', STR_PAD_LEFT);
        }

        Customer::create([
            ...collect($this->only([
                'customer_number',
                'name',
                'gender',
                'phone',
                'mobile',
                'whatsapp',
                'email',
                'address',
                'building_number',
                'street_name',
                'district',
                'city',
                'country_id',
                'status',
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

        $this->customer->update([
            ...collect($this->only([
                'customer_number',
                'name',
                'gender',
                'phone',
                'mobile',
                'whatsapp',
                'email',
                'address',
                'building_number',
                'street_name',
                'district',
                'city',
                'country_id',
                'status',
                'notes',
            ]))->map(fn($v) => $v === '' ? null : $v)->toArray(),
            'updated_by' => auth()->id(),
        ]);

        $this->reset();
    }
}
