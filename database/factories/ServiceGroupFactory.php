<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceGroup>
 */
class ServiceGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $serviceNames = [
            'تأشيرات سياحية',
            'حجز طيران',
            'فنادق ومنتجعات',
            'نقل سياحي',
            'جولات حرة',
            'تأمين سفر',
            'خدمات كبار الشخصيات',
            'عروض شهر العسل'
        ];

        $name = $this->faker->randomElement($serviceNames);

        return [
            'parent_id'   => null, // افتراضياً هي مجموعة أب
            'name'        => $name,
            'description' => $this->faker->sentence(10),
            'requirements' => $this->faker->sentence(10),
            'status'      => $this->faker->randomElement([ActiveStatus::ACTIVE, ActiveStatus::INACTIVE]),
            'created_by'  => 1, // افترضنا وجود مستخدم برقم 1
            'updated_by'  => null,
        ];
    }
}
