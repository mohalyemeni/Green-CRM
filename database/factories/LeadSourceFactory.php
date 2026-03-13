<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\ActiveStatus;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeadSource>
 */
class LeadSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // مصفوفة من المصادر الشائعة لجعل البيانات الوهمية تبدو واقعية
        $commonSources = [
            ['ar' => 'فيسبوك', 'en' => 'Facebook', 'icon' => 'ri-facebook-fill', 'color' => '#1877F2'],
            ['ar' => 'واتساب', 'en' => 'WhatsApp', 'icon' => 'ri-whatsapp-line', 'color' => '#25D366'],
            ['ar' => 'جوجل', 'en' => 'Google Search', 'icon' => 'ri-google-fill', 'color' => '#DB4437'],
            ['ar' => 'تيك توك', 'en' => 'TikTok', 'icon' => 'ri-tiktok-line', 'color' => '#000000'],
            ['ar' => 'انستجرام', 'en' => 'Instagram', 'icon' => 'ri-instagram-line', 'color' => '#E4405F'],
            ['ar' => 'توصية عميل', 'en' => 'Referral', 'icon' => 'ri-user-voice-line', 'color' => '#F1C40F'],
            ['ar' => 'اتصال بارد', 'en' => 'Cold Calling', 'icon' => 'ri-phone-line', 'color' => '#34495E'],
            ['ar' => 'المعرض السنوي', 'en' => 'Annual Exhibition', 'icon' => 'ri-building-line', 'color' => '#8E44AD'],
        ];

        $source = fake()->randomElement($commonSources);

        return [
            // البيانات الأساسية
            'name'        => $source['ar'],
            'name_en'     => $source['en'],
            'code'        => strtoupper(fake()->unique()->lexify('SRC-????')),

            // الوصف: نص عشوائي مختصر
            'description' => fake('ar_SA')->realText(100),

            // خيارات الواجهة
            'icon'        => $source['icon'],
            'color'       => $source['color'],

            // الحالة والترتيب
            'status'      => fake()->boolean(90) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,
            'sort_order'  => fake()->numberBetween(1, 100),

            // بيانات التتبع (نفترض أن المستخدم 1 هو المسؤول)
            'created_by'  => 1,
            'updated_by'  => 1,
        ];
    }
}
