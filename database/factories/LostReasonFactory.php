<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LostReason>
 */
class LostReasonFactory extends Factory
{
    /**
     * اسم الموديل المرتبط بالـ Factory
     */
    protected $model = LostReason::class;

    /**
     * تعريف البيانات الافتراضية
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // مصفوفة لأسباب خسارة الصفقات الشائعة
        $reasons = [
            ['ar' => 'السعر مرتفع جداً', 'en' => 'Price too high', 'code' => 'PRICE'],
            ['ar' => 'تم اختيار المنافس', 'en' => 'Competitor selected', 'code' => 'COMPETITOR'],
            ['ar' => 'ضعف التواصل من العميل', 'en' => 'Poor communication from client', 'code' => 'COMM'],
            ['ar' => 'المواصفات غير مطابقة', 'en' => 'Specs not matching', 'code' => 'SPECS'],
            ['ar' => 'تأجيل المشروع لأجل غير مسمى', 'en' => 'Project postponed indefinitely', 'code' => 'POSTPONED'],
            ['ar' => 'عدم توفر الميزانية حالياً', 'en' => 'No budget available', 'code' => 'BUDGET'],
            ['ar' => 'العميل لا يجيب', 'en' => 'Customer not responding', 'code' => 'NO_REPLY'],
            ['ar' => 'تغيير في متطلبات العميل', 'en' => 'Change in client requirements', 'code' => 'REQ_CHANGE'],
        ];

        $reason = fake()->randomElement($reasons);

        return [
            // البيانات الأساسية
            'name'        => $reason['ar'],
            'name_en'     => $reason['en'],
            'code'        => strtoupper($reason['code'] . '-' . fake()->unique()->numberBetween(100, 999)),

            // الوصف
            'description' => fake('ar_SA')->realText(100),

            // الترتيب والحالة
            'sort_order'  => fake()->numberBetween(1, 100),
            'status'      => fake()->boolean(90) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,

            // بيانات التتبع
            'created_by'  => 1,
            'updated_by'  => 1,
        ];
    }
}
