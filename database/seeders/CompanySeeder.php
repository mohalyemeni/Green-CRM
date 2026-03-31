<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // جلب معرف الريال اليمني (العملة المحلية) الذي تم إنشاؤه في CurrencySeeder
        $baseCurrencyId = DB::table('currencies')->where('code', 'YER')->value('id');

        // في حال لم يتم تشغيل CurrencySeeder بعد، نضع قيمة افتراضية أو نتحقق
        if (!$baseCurrencyId) {
            $this->command->error('يرجى تشغيل CurrencySeeder أولاً لضمان وجود العملة الأساسية!');
            return;
        }

        DB::table('companies')->insert([
            [
                'name' => 'شركة الارض الخضراء',
                'name_en' => 'Green Land Company',
                'short_name' => 'Green Land',
                'slug' => Str::slug('Green Land Company'), // al-masar-systems
                'website' => 'https://green-land.com',
                'logo' => 'logos/default-company-logo.png',
                'base_currency_id' => $baseCurrencyId,
                'status' => ActiveStatus::ACTIVE->value,
                'notes' => 'الفرع الرئيسي - الإدارة العامة',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
