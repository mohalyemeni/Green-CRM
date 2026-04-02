<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'           => fake('ar_SA')->country(), // اسم الدولة بالعربي
            'name_en'        => fake()->country(),        // اسم الدولة بالإنجليزي
            'country_code'   => fake()->unique()->countryCode(), // كود من حرفين
            'phone_code'     => '+' . fake()->numberBetween(1, 999), // مفتاح الاتصال
            'nationality'    => 'عربي',
            'nationality_en' => 'Arab',
            'status'         => \App\Enums\ActiveStatus::ACTIVE->value, // الحالة مفعل كافتراضي
            'created_by'     => 1,
            'updated_by'     => 1,
        ];
    }
}
