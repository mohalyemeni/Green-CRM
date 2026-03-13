<?php

namespace App\Livewire\Forms;

use App\Enums\ActiveStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;
use App\Models\Country;

class CountriesForm extends Form
{
    public ?Country $country = null;

    // ===== الخصائص (Properties) =====

    public string  $name               = '';
    public ?string $name_en            = null;
    public ?string $country_code       = null;
    public ?string $phone_code         = null;
    public ?string $nationality        = null;
    public ?string $nationality_en     = null;

    public int     $status             = ActiveStatus::ACTIVE->value; // 1 = ActiveStatus::ACTIVE

    // ===== قواعد التحقق (Validation Rules) =====

    public function rules(): array
    {
        $countryId = $this->country?->id;

        return [
            'name'               => 'required|string|min:2|max:150',
            'name_en'            => 'nullable|string|max:150',

            // كود الدولة يجب أن يكون فريداً ويتكون من حرفين فقط (مثال: YE)
            'country_code'       => 'nullable|string|size:2|unique:countries,country_code,' . $countryId,

            'phone_code'         => 'nullable|string|max:20',
            'nationality'        => 'nullable|string|max:150',
            'nationality_en'     => 'nullable|string|max:150',

            'status'             => 'required|in:0,1',
        ];
    }

    // ===== أسماء الحقول (Validation Attributes) =====

    public function validationAttributes(): array
    {
        return [
            'name'               => 'اسم الدولة (بالعربية)',
            'name_en'            => 'اسم الدولة (بالإنجليزية)',
            'country_code'       => 'كود الدولة',
            'phone_code'         => 'مفتاح الاتصال الدولي',
            'nationality'        => 'الجنسية (بالعربية)',
            'nationality_en'     => 'الجنسية (بالإنجليزية)',
            'status'             => 'الحالة',
        ];
    }

    // ===== رسائل التحقق (Validation Messages) =====

    public function messages(): array
    {
        return [
            'name.required'      => 'اسم الدولة مطلوب.',
            'name.min'           => 'اسم الدولة يجب أن يكون حرفين على الأقل.',
            'country_code.size'  => 'كود الدولة يجب أن يتكون من حرفين فقط (مثال: YE).',
            'country_code.unique' => 'كود الدولة هذا مسجل مسبقاً لدولة أخرى.',
            'status.required'    => 'حالة الدولة مطلوبة.',
            'status.in'          => 'قيمة الحالة غير صحيحة.',
        ];
    }

    // ===== تعيين دولة موجودة للتعديل =====

    public function setCountry(Country $country): void
    {
        $this->country            = $country;

        $this->name               = $country->name;
        $this->name_en            = $country->name_en;
        $this->country_code       = $country->country_code;
        $this->phone_code         = $country->phone_code;
        $this->nationality        = $country->nationality;
        $this->nationality_en     = $country->nationality_en;

        // معالجة القيمة سواء كانت Enum أو Integer
        $this->status             = isset($country->status->value) ? $country->status->value : $country->status;
    }

    // ===== حفظ دولة جديدة =====

    public function store(): void
    {
        $this->validate();

        Country::create([
            'name'               => $this->name ?: null,
            'name_en'            => $this->name_en ?: null,
            // التأكد من تحويل كود الدولة لحروف كبيرة دائماً (مثال: ye يتحول إلى YE)
            'country_code'       => $this->country_code ? strtoupper($this->country_code) : null,
            'phone_code'         => $this->phone_code ?: null,
            'nationality'        => $this->nationality ?: null,
            'nationality_en'     => $this->nationality_en ?: null,
            'status'             => $this->status,
            'created_by'         => auth()->id(),
        ]);

        $this->reset();
    }

    // ===== تحديث دولة موجودة =====

    public function update(): void
    {
        $this->validate();

        $this->country->update([
            'name'               => $this->name ?: null,
            'name_en'            => $this->name_en ?: null,
            'country_code'       => $this->country_code ? strtoupper($this->country_code) : null,
            'phone_code'         => $this->phone_code ?: null,
            'nationality'        => $this->nationality ?: null,
            'nationality_en'     => $this->nationality_en ?: null,
            'status'             => $this->status,
            'updated_by'         => auth()->id(),
        ]);

        $this->reset();
    }
}
