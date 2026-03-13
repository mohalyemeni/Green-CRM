<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ActiveStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Industry>
 */
class IndustryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // البيانات الأساسية للقطاع
            // نستخدم كلمات عشوائية لتمثيل أسماء القطاعات (مثل: تقنية، تعليم، تجارة)
            'name'        => fake('ar_SA')->unique()->word() . ' ' . fake('ar_SA')->word(),
            'name_en'     => fake('en_US')->unique()->jobTitle(), // مسمى وظيفي أو قطاع بالإنجليزية

            // وصف القطاع (نص عربي مختصر)
            'description' => fake('ar_SA')->realText(150),

            // الأيقونة: نخزن أسماء كلاسات Remix Icon الشهيرة بشكل عشوائي
            'icon'        => fake()->randomElement([
                'ri-building-line',
                'ri-hospital-line',
                'ri-store-2-line',
                'ri-computer-line',
                'ri-bank-line',
                'ri-truck-line',
                'ri-customer-service-2-line',
                'ri-shopping-basket-2-line'
            ]),

            // الحالة: 90% مفعل
            'status'      => fake()->boolean(90) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,

            // ترتيب العرض: رقم عشوائي بين 1 و 50
            'sort_order'  => fake()->numberBetween(1, 50),

            // التتبع (بافتراض أن المستخدم رقم 1 هو المدير العام)
            'created_by'  => 1,
            'updated_by'  => 1,
        ];
    }
}
