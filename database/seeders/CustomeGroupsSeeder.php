<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomeGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'عملاء الأفراد',
                'name_en' => 'Retail Customers',
                'code' => 'CG-RET',
                'description' => 'العملاء المباشرين لحجوزات الطيران الفردية، الفنادق، والخدمات السياحية العادية بنظام الدفع الفوري.',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الشركات والمؤسسات (B2B)',
                'name_en' => 'Corporate Clients',
                'code' => 'CG-CORP',
                'description' => 'الجهات التجارية التي تمتلك عقود عمل ومطالبات مالية دورية (آجلة) لحجوزات سفر موظفيها.',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'الوكلاء الفرعيين',
                'name_en' => 'Sub-Agents',
                'code' => 'CG-AGT',
                'description' => 'مكاتب السياحة الصغيرة أو الوسطاء الذين ينفذون حجوزاتهم عبر المكتب بنظام العمولات (Commission).',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'عملاء الحج والعمرة',
                'name_en' => 'Hajj & Umrah',
                'code' => 'CG-REL',
                'description' => 'مجموعات مخصصة لبرامج العمرة والحج، تتطلب متابعة خاصة لمسارات التأشيرات وتصاريح المرور.',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'ركاب النقل البري',
                'name_en' => 'Land Transport Passengers',
                'code' => 'CG-LND',
                'description' => 'العملاء المستفيدين من خدمات النقل الجماعي الدولي (مثل رحلات الباصات الدولية) وخدمات الشحن المرافقة.',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'معاملات الفيز والسفارات',
                'name_en' => 'Visa & Embassy Services',
                'code' => 'CG-VIS',
                'description' => 'العملاء الذين يقتصر تعاملهم على تخليص المعاملات القنصلية، تجديد الجوازات، وتأشيرات الزيارة والعمل.',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'عملاء كبار الشخصيات (VIP)',
                'name_en' => 'VIP Clients',
                'code' => 'CG-VIP',
                'description' => 'عملاء متميزون يحصلون على أسعار خاصة، خدمات الكونسيرج، وترقيات لدرجات الطيران والفنادق.',
                'status' => ActiveStatus::ACTIVE->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // استخدام upsert لمنع تكرار البيانات في حال تشغيل Seeder أكثر من مرة بناءً على حقل code
        DB::table('customer_groups')->upsert(
            $groups,
            ['code'], // الحقل الفريد الذي يتم التحقق منه
            ['name', 'name_en', 'description', 'status', 'updated_at'] // الحقول التي يتم تحديثها إذا كان الكود موجوداً
        );
    }
}
