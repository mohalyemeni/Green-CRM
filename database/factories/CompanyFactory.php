<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // البيانات الأساسية للشركة
            'name'             => fake('ar_SA')->company(), // اسم شركة عربي
            'name_en'          => fake('en_US')->company(), // اسم شركة إنجليزي
            'short_name'       => fake()->lexify('?????'), // اسم مختصر من 5 أحرف عشوائية

            // روابط النظام (نولد رابط فريد بناءً على كلمات إنجليزية لتجنب مشاكل الروابط العربية)
            'slug'             => fake()->unique()->slug(2),

            // بيانات الاتصال والهوية
            'website'          => 'https://www.' . fake()->domainName(),
            'logo'             => fake()->imageUrl(200, 200, 'business', true, 'Logo'), // مسار وهمي لصورة شعار

            // الإعدادات المحاسبية (العملة الأساسية)
            // بافتراض أنك قمت بعمل Seeder للعملات مسبقاً ولديك عملات بأرقام 1 إلى 5
            'base_currency_id' => fake()->numberBetween(1, 5),

            // الحالة والملاحظات
            // نعطي احتمال 90% أن تكون الشركة مفعلة (1) و 10% أن تكون موقوفة (0)
            'status'           => fake()->boolean(90) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,
            'notes'            => fake()->optional()->sentence(), // optional تجعل الحقل أحياناً null وأحياناً نص

            // التتبع (بافتراض أن المستخدم رقم 1 هو الآدمن)
            'created_by'       => 1,
            'updated_by'       => 1,
        ];
    }
}
