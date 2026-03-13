<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OpportinitySourc>
 */
class OpportunitySourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /**
     * اسم الموديل المرتبط بالـ Factory
     */
    protected $model = OpportunitySource::class;

    /**
     * تعريف البيانات الافتراضية
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // قائمة بمصادر الفرص البيعية الشائعة (B2B & B2C)
        $opportunitySources = [
            ['ar' => 'مناقصة حكومية', 'en' => 'Government Tender', 'icon' => 'ri-government-line', 'color' => '#2c3e50'],
            ['ar' => 'مؤتمر تجاري', 'en' => 'Trade Show', 'icon' => 'ri-mic-line', 'color' => '#e67e22'],
            ['ar' => 'بحث مباشر (Outbound)', 'en' => 'Direct Outreach', 'icon' => 'ri-focus-3-line', 'color' => '#3498db'],
            ['ar' => 'موقع الشركة الإلكتروني', 'en' => 'Corporate Website', 'icon' => 'ri-global-line', 'color' => '#27ae60'],
            ['ar' => 'شريك استراتيجي', 'en' => 'Strategic Partner', 'icon' => 'ri-handshake-line', 'color' => '#9b59b6'],
            ['ar' => 'حملة بريد إلكتروني', 'en' => 'Email Campaign', 'icon' => 'ri-mail-send-line', 'color' => '#e74c3c'],
            ['ar' => 'توصية مجلس الإدارة', 'en' => 'Board Referral', 'icon' => 'ri-user-star-line', 'color' => '#f1c40f'],
            ['ar' => 'ندوة عبر الإنترنت', 'en' => 'Webinar', 'icon' => 'ri-video-chat-line', 'color' => '#1abc9c'],
        ];

        $source = fake()->randomElement($opportunitySources);

        return [
            // البيانات الأساسية
            'name'        => $source['ar'],
            'name_en'     => $source['en'],
            'code'        => strtoupper(fake()->unique()->lexify('OPP-????')),
            
            // الوصف
            'description' => fake('ar_SA')->realText(120),

            // خيارات التنسيق
            'icon'        => $source['icon'],
            'color'       => $source['color'],
            
            // الحالة والترتيب
            'status'      => fake()->boolean(95) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,
            'sort_order'  => fake()->numberBetween(1, 50),

            // بيانات التتبع
            'created_by'  => 1,
            'updated_by'  => 1,
        ];
    }
}
