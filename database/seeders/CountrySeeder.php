<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Enums\ActiveStatus;
use Carbon\Carbon;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // ==========================================
            // الدول ذات الأولوية
            // ==========================================
            ['name' => 'اليمن', 'name_en' => 'Yemen', 'country_code' => 'YE', 'phone_code' => '+967', 'nationality' => 'يمني', 'nationality_en' => 'Yemeni'],
            ['name' => 'المملكة العربية السعودية', 'name_en' => 'Saudi Arabia', 'country_code' => 'SA', 'phone_code' => '+966', 'nationality' => 'سعودي', 'nationality_en' => 'Saudi'],

            // ==========================================
            // بقية الدول العربية
            // ==========================================
            ['name' => 'الإمارات العربية المتحدة', 'name_en' => 'United Arab Emirates', 'country_code' => 'AE', 'phone_code' => '+971', 'nationality' => 'إماراتي', 'nationality_en' => 'Emirati'],
            ['name' => 'مصر', 'name_en' => 'Egypt', 'country_code' => 'EG', 'phone_code' => '+20', 'nationality' => 'مصري', 'nationality_en' => 'Egyptian'],
            ['name' => 'الكويت', 'name_en' => 'Kuwait', 'country_code' => 'KW', 'phone_code' => '+965', 'nationality' => 'كويتي', 'nationality_en' => 'Kuwaiti'],
            ['name' => 'قطر', 'name_en' => 'Qatar', 'country_code' => 'QA', 'phone_code' => '+974', 'nationality' => 'قطري', 'nationality_en' => 'Qatari'],
            ['name' => 'البحرين', 'name_en' => 'Bahrain', 'country_code' => 'BH', 'phone_code' => '+973', 'nationality' => 'بحريني', 'nationality_en' => 'Bahraini'],
            ['name' => 'سلطنة عمان', 'name_en' => 'Oman', 'country_code' => 'OM', 'phone_code' => '+968', 'nationality' => 'عماني', 'nationality_en' => 'Omani'],
            ['name' => 'الأردن', 'name_en' => 'Jordan', 'country_code' => 'JO', 'phone_code' => '+962', 'nationality' => 'أردني', 'nationality_en' => 'Jordanian'],
            ['name' => 'سوريا', 'name_en' => 'Syria', 'country_code' => 'SY', 'phone_code' => '+963', 'nationality' => 'سوري', 'nationality_en' => 'Syrian'],
            ['name' => 'لبنان', 'name_en' => 'Lebanon', 'country_code' => 'LB', 'phone_code' => '+961', 'nationality' => 'لبناني', 'nationality_en' => 'Lebanese'],
            ['name' => 'فلسطين', 'name_en' => 'Palestine', 'country_code' => 'PS', 'phone_code' => '+970', 'nationality' => 'فلسطيني', 'nationality_en' => 'Palestinian'],
            ['name' => 'العراق', 'name_en' => 'Iraq', 'country_code' => 'IQ', 'phone_code' => '+964', 'nationality' => 'عراقي', 'nationality_en' => 'Iraqi'],
            ['name' => 'السودان', 'name_en' => 'Sudan', 'country_code' => 'SD', 'phone_code' => '+249', 'nationality' => 'سوداني', 'nationality_en' => 'Sudanese'],
            ['name' => 'ليبيا', 'name_en' => 'Libya', 'country_code' => 'LY', 'phone_code' => '+218', 'nationality' => 'ليبي', 'nationality_en' => 'Libyan'],
            ['name' => 'تونس', 'name_en' => 'Tunisia', 'country_code' => 'TN', 'phone_code' => '+216', 'nationality' => 'تونسي', 'nationality_en' => 'Tunisian'],
            ['name' => 'الجزائر', 'name_en' => 'Algeria', 'country_code' => 'DZ', 'phone_code' => '+213', 'nationality' => 'جزائري', 'nationality_en' => 'Algerian'],
            ['name' => 'المغرب', 'name_en' => 'Morocco', 'country_code' => 'MA', 'phone_code' => '+212', 'nationality' => 'مغربي', 'nationality_en' => 'Moroccan'],
            ['name' => 'موريتانيا', 'name_en' => 'Mauritania', 'country_code' => 'MR', 'phone_code' => '+222', 'nationality' => 'موريتاني', 'nationality_en' => 'Mauritanian'],
            ['name' => 'الصومال', 'name_en' => 'Somalia', 'country_code' => 'SO', 'phone_code' => '+252', 'nationality' => 'صومالي', 'nationality_en' => 'Somali'],
            ['name' => 'جيبوتي', 'name_en' => 'Djibouti', 'country_code' => 'DJ', 'phone_code' => '+253', 'nationality' => 'جيبوتي', 'nationality_en' => 'Djiboutian'],
            ['name' => 'جزر القمر', 'name_en' => 'Comoros', 'country_code' => 'KM', 'phone_code' => '+269', 'nationality' => 'قمري', 'nationality_en' => 'Comoran'],

            // ==========================================
            // أبرز الدول الأجنبية
            // ==========================================
            ['name' => 'الولايات المتحدة', 'name_en' => 'United States', 'country_code' => 'US', 'phone_code' => '+1', 'nationality' => 'أمريكي', 'nationality_en' => 'American'],
            ['name' => 'المملكة المتحدة', 'name_en' => 'United Kingdom', 'country_code' => 'GB', 'phone_code' => '+44', 'nationality' => 'بريطاني', 'nationality_en' => 'British'],
            ['name' => 'تركيا', 'name_en' => 'Turkey', 'country_code' => 'TR', 'phone_code' => '+90', 'nationality' => 'تركي', 'nationality_en' => 'Turkish'],
            ['name' => 'الصين', 'name_en' => 'China', 'country_code' => 'CN', 'phone_code' => '+86', 'nationality' => 'صيني', 'nationality_en' => 'Chinese'],
            ['name' => 'الهند', 'name_en' => 'India', 'country_code' => 'IN', 'phone_code' => '+91', 'nationality' => 'هندي', 'nationality_en' => 'Indian'],
            ['name' => 'ماليزيا', 'name_en' => 'Malaysia', 'country_code' => 'MY', 'phone_code' => '+60', 'nationality' => 'ماليزي', 'nationality_en' => 'Malaysian'],
            ['name' => 'إندونيسيا', 'name_en' => 'Indonesia', 'country_code' => 'ID', 'phone_code' => '+62', 'nationality' => 'إندونيسي', 'nationality_en' => 'Indonesian'],
            ['name' => 'فرنسا', 'name_en' => 'France', 'country_code' => 'FR', 'phone_code' => '+33', 'nationality' => 'فرنسي', 'nationality_en' => 'French'],
            ['name' => 'ألمانيا', 'name_en' => 'Germany', 'country_code' => 'DE', 'phone_code' => '+49', 'nationality' => 'ألماني', 'nationality_en' => 'German'],
            ['name' => 'روسيا', 'name_en' => 'Russia', 'country_code' => 'RU', 'phone_code' => '+7', 'nationality' => 'روسي', 'nationality_en' => 'Russian'],
            ['name' => 'كندا', 'name_en' => 'Canada', 'country_code' => 'CA', 'phone_code' => '+1', 'nationality' => 'كندي', 'nationality_en' => 'Canadian'],
            ['name' => 'أستراليا', 'name_en' => 'Australia', 'country_code' => 'AU', 'phone_code' => '+61', 'nationality' => 'أسترالي', 'nationality_en' => 'Australian'],
            ['name' => 'اليابان', 'name_en' => 'Japan', 'country_code' => 'JP', 'phone_code' => '+81', 'nationality' => 'ياباني', 'nationality_en' => 'Japanese'],
            ['name' => 'إيطاليا', 'name_en' => 'Italy', 'country_code' => 'IT', 'phone_code' => '+39', 'nationality' => 'إيطالي', 'nationality_en' => 'Italian'],
            ['name' => 'إسبانيا', 'name_en' => 'Spain', 'country_code' => 'ES', 'phone_code' => '+34', 'nationality' => 'إسباني', 'nationality_en' => 'Spanish'],
            ['name' => 'البرازيل', 'name_en' => 'Brazil', 'country_code' => 'BR', 'phone_code' => '+55', 'nationality' => 'برازيلي', 'nationality_en' => 'Brazilian'],
            ['name' => 'باكستان', 'name_en' => 'Pakistan', 'country_code' => 'PK', 'phone_code' => '+92', 'nationality' => 'باكستاني', 'nationality_en' => 'Pakistani'],
        ];

        // تجهيز مصفوفة الإدخال النهائية مع الحقول المشتركة
        $now = Carbon::now();
        $insertData = [];

        foreach ($countries as $country) {
            $insertData[] = array_merge($country, [
                'status'     => ActiveStatus::ACTIVE->value,
                'created_by' => 1, // بافتراض أن الـ Super Admin رقمه 1
                'updated_by' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // تفريغ الجدول لتجنب التكرار (اختياري)
        DB::table('countries')->truncate();

        // إدخال البيانات دفعة واحدة (Bulk Insert) لتحقيق أقصى سرعة
        DB::table('countries')->insert($insertData);
    }
}
