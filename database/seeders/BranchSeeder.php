<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. جلب بيانات الشركة والعملة لربطها بالفرع
        $companyId = DB::table('companies')->where('slug', 'al-masar-systems')->value('id');
        $currencyId = DB::table('currencies')->where('code', 'YER')->value('id');

        // ملاحظة: إذا لم يكن لديك جدول دول مفعل، اترك country_id كـ null أو تأكد من وجوده
        $countryId = DB::table('countries')->where('iso_code', 'YE')->value('id');

        if (!$companyId) {
            $this->command->error('يجب تشغيل CompanySeeder أولاً!');
            return;
        }

        DB::table('branches')->insert([
            [
                'company_id' => $companyId,
                'code' => 'BR-001',
                'name' => 'المركز الرئيسي',
                'name_en' => 'Main Branch',
                'slug' => Str::slug('Main Branch'),
                'commercial_register' => '123456789',
                'tax_number' => '987654321',
                'country_id' => $countryId,
                'state' => 'إب',
                'city' => 'إب',
                'district' => 'شارع العدين',
                'street_address' => 'بجوار جامعة إب',
                'timezone' => 'Asia/Riyadh',
                'currency_id' => $currencyId,
                'phone' => '01234567',
                'mobile' => '777777777',
                'email' => 'contact@green-land.com',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
