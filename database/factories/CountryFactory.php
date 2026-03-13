<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Country>
 */
class CountryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // أسماء الدولة
            'name'           => fake('ar_SA')->country(), // اسم دولة عشوائي بالعربية
            'name_en'        => fake('en_US')->country(), // اسم دولة عشوائي بالإنجليزية

            // الأكواد الدولية
            // دالة countryCode() تولد كوداً من حرفين (مثال: US, UK) وتجعلها unique لعدم التكرار
            'country_code'   => fake()->unique()->countryCode(),
            // توليد مفتاح اتصال دولي وهمي (من +1 إلى +999)
            'phone_code'     => '+' . fake()->numberBetween(1, 999),

            // الجنسيات (بيانات وهمية تقريبية للاختبار)
            'nationality'    => fake('ar_SA')->word() . 'ي',
            'nationality_en' => fake('en_US')->word() . 'ian',

            // الحالة (احتمال 90% أن تكون مفعلة)
            'status'         => fake()->boolean(90) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,

            // التتبع (بافتراض أن المستخدم رقم 1 هو الآدمن)
            'created_by'     => 1,
            'updated_by'     => 1,
        ];
    }
}
