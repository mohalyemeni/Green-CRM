<?php

namespace Database\Seeders;

use App\Enums\ActiveStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'عميل محتمل',
                'name_en' => 'Lead',
                'code' => 'LS_LEAD',
                'description' => 'عميل محتمل تواصل حديثاً (رسالة، اتصال، أو نموذج ويب) ولم يتم الرد عليه بعد.',
                'color' => '#3B82F6', // أزرق
                'is_default' => true,  // الحالة الافتراضية
                'is_closed' => false,
                'status' => ActiveStatus::ACTIVE->value,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'تم التواصل / قيد النقاش',
                'name_en' => 'Contacted / In Discussion',
                'code' => 'LS_CONTACTED',
                'description' => 'تم الرد على العميل ويتم مناقشة تفاصيل الرحلة، التأشيرة، أو الوجهة المفضلة.',
                'color' => '#F59E0B', // برتقالي
                'is_default' => false,
                'is_closed' => false,
                'status' => ActiveStatus::ACTIVE->value,
                'sort_order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'قيد المتابعة',
                'name_en' => 'In Progress',
                'code' => 'LS_IN_PROGRESS',
                'description' => 'يتم حالياً التواصل المستمر مع العميل لمتابعة عرض السعر المرسل أو استكمال متطلبات الحجز.',
                'color' => '#8B5CF6', // بنفسجي (يمكنك تغييره حسب الرغبة)
                'is_default' => false,
                'is_closed' => false,
                'status' => ActiveStatus::ACTIVE->value,
                'sort_order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'متابعة لاحقة',
                'name_en' => 'Follow Up',
                'code' => 'LS_FOLLOW_UP',
                'description' => 'تم إرسال عرض السعر للعميل وهو بحاجة للتفكير أو طلب المتابعة في وقت لاحق لتأكيد الحجز.',
                'color' => '#14B8A6', // تركوازي (Teal)
                'is_default' => false,
                'is_closed' => false,
                'status' => ActiveStatus::ACTIVE->value,
                'sort_order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مغلق - تم التحويل لعميل',
                'name_en' => 'Closed - Won (Converted)',
                'code' => 'LS_CONVERTED',
                'description' => 'اكتملت الصفقة بنجاح وتم تسجيله كعميل فعلي في نظام الحجوزات أو التذاكر.',
                'color' => '#10B981', // أخضر زمردي
                'is_default' => false,
                'is_closed' => true, // إغلاق الملف كفرصة ناجحة
                'status' => ActiveStatus::ACTIVE->value,
                'sort_order' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'مغلق - فُقدت الفرصة',
                'name_en' => 'Closed - Lost / Canceled',
                'code' => 'LS_LOST',
                'description' => 'العميل تراجع عن فكرة السفر، حجز مع منافس، أو وجد الأسعار غير مناسبة.',
                'color' => '#EF4444', // أحمر (Red)
                'is_default' => false,
                'is_closed' => true, // إغلاق الملف بخسارة
                'status' => ActiveStatus::ACTIVE->value,
                'sort_order' => 70,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'غير مؤهل / معلومات خاطئة',
                'name_en' => 'Unqualified / Junk',
                'code' => 'LS_JUNK',
                'description' => 'طلب غير جدي، رقم هاتف وهمي، أو طلب فيزا تم رفضه مسبقاً بشكل قاطع (غير مؤهل).',
                'color' => '#6B7280', // رمادي (Gray)
                'is_default' => false,
                'is_closed' => true, // استبعاد الملف فوراً
                'status' => ActiveStatus::ACTIVE->value,
                'sort_order' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('lead_statuses')->upsert(
            $statuses,
            ['code'], // كود الحالة هو المفتاح الفريد عند التحديث
            ['name', 'name_en', 'description', 'color', 'is_default', 'is_closed', 'sort_order', 'status', 'updated_at']
        );
    }
}
