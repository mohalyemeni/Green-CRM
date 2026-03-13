<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadStatus>
 */
class LeadStatusFactory extends Factory
{
    /**
     * اسم الموديل المرتبط بالـ Factory
     */
    protected $model = LeadStatus::class;

    /**
     * تعريف البيانات الافتراضية للموديل
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // مصفوفة لحالات العملاء المحتملين النموذجية
        $statuses = [
            ['ar' => 'جديد', 'en' => 'New', 'code' => 'NEW', 'color' => '#3577f1', 'is_default' => true, 'is_closed' => false],
            ['ar' => 'قيد التواصل', 'en' => 'In Contact', 'code' => 'CONTACTED', 'color' => '#00d084', 'is_default' => false, 'is_closed' => false],
            ['ar' => 'محاولة تواصل', 'en' => 'Attempted', 'code' => 'ATTEMPTED', 'color' => '#ffbf00', 'is_default' => false, 'is_closed' => false],
            ['ar' => 'مهتم', 'en' => 'Interested', 'code' => 'INTERESTED', 'color' => '#0ab39c', 'is_default' => false, 'is_closed' => false],
            ['ar' => 'غير مؤهل', 'en' => 'Unqualified', 'code' => 'UNQUALIFIED', 'color' => '#f06548', 'is_default' => false, 'is_closed' => true],
            ['ar' => 'تم التحويل لفرصة', 'en' => 'Converted', 'code' => 'CONVERTED', 'color' => '#405189', 'is_default' => false, 'is_closed' => true],
        ];

        $statusInfo = fake()->randomElement($statuses);

        return [
            // البيانات الأساسية
            'name'        => $statusInfo['ar'],
            'name_en'     => $statusInfo['en'],
            'code'        => strtoupper($statusInfo['code']),
            'description' => fake('ar_SA')->realText(100),
            'color'       => $statusInfo['color'],

            // إعدادات الحالة
            'is_default'  => $statusInfo['is_default'],
            'is_closed'   => $statusInfo['is_closed'],

            // الحالة والترتيب
            'status'      => ActiveStatus::ACTIVE->value, // الحالات يجب أن تكون مفعلة افتراضياً
            'sort_order'  => fake()->numberBetween(1, 10),

            // التتبع
            'created_by'  => 1,
            'updated_by'  => 1,
        ];
    }
}
