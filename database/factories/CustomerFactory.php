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
            // البيانات الأساسية
            'customer_number' => 'CUST-' . fake()->unique()->numberBetween(1000, 9999),
            'name'            => fake()->name(),
            'national_id'     => fake()->unique()->numerify('##########'),
            'age'             => fake()->numberBetween(18, 80),
            'gender'          => fake()->randomElement([1, 2]), // 1=ذكر، 2=أنثى

            // بيانات العنوان
            'general_address' => fake()->address(),
            'building_number' => fake()->buildingNumber(),
            'street_name'     => fake()->streetName(),
            'district'        => fake()->citySuffix(),
            'city'            => fake()->city(),
            'country'         => fake()->randomElement([
                'المملكة العربية السعودية',
                'اليمن',
                'مصر',
                'قطر',
                'البحرين',
                'الكويت'
            ]), // أو fake()->country()

            // بيانات التواصل
            'mobile'          => fake()->phoneNumber(),
            'email'           => fake()->unique()->safeEmail(),

            // البيانات المالية
            'tax_number'      => fake()->numerify('3###########003'),
            'dealing_method'  => fake()->randomElement(['كاش', 'آجل']),
            'credit_limit'    => fake()->randomFloat(2, 0, 50000), // حد دين بين 0 و 50 ألف

            // الحالة والملاحظات
            'status'       => fake()->randomElement([1, 2, 3]), // 90% احتمال أن يكون نشطاً
            'notes'           => fake()->sentence(),

            // التتبع (ربط مع مستخدمين موجودين أو إنشاء مستخدمين جدد)
            'created_by'      => 1,
            'updated_by'      => 1,
        ];
    }
}
