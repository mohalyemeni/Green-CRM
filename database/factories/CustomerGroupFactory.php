<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CustomerGroup>
 */
class CustomerGroupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // البيانات الأساسية للمجموعة
            // توليد اسم عشوائي (مثال: مجموعة المجد، مجموعة الأفق...)
            'name'        => 'مجموعة ' . fake('ar_SA')->word(),
            'name_en'     => fake('en_US')->word() . ' Group',

            // توليد كود مميز للمجموعة (مثال: CG-4921) (CG = Customer Group)
            'code'        => fake()->unique()->bothify('CG-####'),

            // وصف المجموعة (استخدمنا optional بنسبة 70% ليكون الحقل أحياناً فارغاً كواقع النظام)
            // realText(100) يولد نصاً عربياً حقيقياً بطول 100 حرف تقريباً
            'description' => fake()->optional(0.7)->realText(100),

            // الحالة والملاحظات
            // نعطي احتمال 90% أن تكون المجموعة مفعلة (1) و 10% أن تكون موقوفة (0)
            'status'      => fake()->boolean(90) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,

            // التتبع (بافتراض أن المستخدم رقم 1 هو الآدمن)
            'created_by'  => 1,
            'updated_by'  => 1,
        ];
    }
}
