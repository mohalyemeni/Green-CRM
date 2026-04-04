<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpportunitySourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sources = [
            ['name' => 'واتساب', 'name_en' => 'WhatsApp', 'code' => 'SRC_WHATSAPP', 'icon' => 'fab fa-whatsapp', 'color' => '#25D366', 'sort_order' => 1],
            ['name' => 'اتصال هاتفي', 'name_en' => 'Phone Call', 'code' => 'SRC_PHONE', 'icon' => 'fas fa-phone-alt', 'color' => '#6366F1', 'sort_order' => 2],
            ['name' => 'إعلانات فيسبوك', 'name_en' => 'Facebook Ads', 'code' => 'SRC_FB_ADS', 'icon' => 'fab fa-facebook', 'color' => '#1877F2', 'sort_order' => 3],
            ['name' => 'وسائل التواصل', 'name_en' => 'Social Media', 'code' => 'SRC_SOCIAL', 'icon' => 'fab fa-share-alt', 'color' => '#8B5CF6', 'sort_order' => 4],
            ['name' => 'عميل حالي', 'name_en' => 'Existing Customer', 'code' => 'SRC_EXISTING', 'icon' => 'fas fa-user-check', 'color' => '#84CC16', 'sort_order' => 5],
            ['name' => 'زيارة مباشرة', 'name_en' => 'Walk-in', 'code' => 'SRC_WALKIN', 'icon' => 'fas fa-walking', 'color' => '#EF4444', 'sort_order' => 6],
            ['name' => 'الموقع الإلكتروني', 'name_en' => 'Website', 'code' => 'SRC_WEB', 'icon' => 'fas fa-globe', 'color' => '#3B82F6', 'sort_order' => 7],
            ['name' => 'البريد الإلكتروني', 'name_en' => 'Email', 'code' => 'SRC_EMAIL', 'icon' => 'fas fa-envelope', 'color' => '#10B981', 'sort_order' => 8],
            ['name' => 'إعلانات جوجل', 'name_en' => 'Google Ads', 'code' => 'SRC_G_ADS', 'icon' => 'fab fa-google', 'color' => '#4285F4', 'sort_order' => 9],
            ['name' => 'توصية (إحالة)', 'name_en' => 'Referral', 'code' => 'SRC_REF', 'icon' => 'fas fa-user-plus', 'color' => '#F59E0B', 'sort_order' => 10],
            ['name' => 'فعالية / معرض', 'name_en' => 'Event', 'code' => 'SRC_EVENT', 'icon' => 'fas fa-calendar-day', 'color' => '#EC4899', 'sort_order' => 11],
            ['name' => 'شريك استراتيجي', 'name_en' => 'Partner', 'code' => 'SRC_PARTNER', 'icon' => 'fas fa-handshake', 'color' => '#14B8A6', 'sort_order' => 12],
            ['name' => 'وكيل بيع', 'name_en' => 'Reseller', 'code' => 'SRC_RESELLER', 'icon' => 'fas fa-store', 'color' => '#D946EF', 'sort_order' => 13],
            ['name' => 'أخرى', 'name_en' => 'Other', 'code' => 'SRC_OTHER', 'icon' => 'fas fa-ellipsis-h', 'color' => '#9CA3AF', 'sort_order' => 14],
        ];

        foreach ($sources as $source) {
            \Illuminate\Support\Facades\DB::table('opportunity_sources')->updateOrInsert(
                ['code' => $source['code']],
                array_merge($source, [
                    'status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}
