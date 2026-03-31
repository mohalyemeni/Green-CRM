<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            [
                'code' => 'YER',
                'name' => 'ريال يمني',
                'symbol' => 'ر.ي',
                'fraction_name' => 'فلس',
                'exchange_rate' => 1.000000,
                'equivalent' => 1.000000,
                'max_exchange_rate' => 1.000000,
                'min_exchange_rate' => 1.000000,
                'is_local' => true,      // العملة المحلية
                'is_inventory' => true,  // عملة المخزون
                'status' => ActiveStatus::ACTIVE->value,           // ActiveStatus::ACTIVE
                'created_at' => now(),
            ],
            [
                'code' => 'SAR',
                'name' => 'ريال سعودي',
                'symbol' => 'ر.س',
                'fraction_name' => 'هللة',
                'exchange_rate' => 140.000000, // سعر الصرف مقابل اليمني
                'equivalent' => 140.000000,
                'max_exchange_rate' => 145.000000,
                'min_exchange_rate' => 139.000000,
                'is_local' => false,
                'is_inventory' => false,
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
            ],
            [
                'code' => 'USD',
                'name' => 'دولار أمريكي',
                'symbol' => '$',
                'fraction_name' => 'سنت',
                'exchange_rate' => 535.000000, // قيمة افتراضية تقريبية
                'equivalent' => 535.000000,
                'max_exchange_rate' => 540.000000,
                'min_exchange_rate' => 530.000000,
                'is_local' => false,
                'is_inventory' => false,
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
            ],
        ];

        DB::table('currencies')->insert($currencies);
    }
}
