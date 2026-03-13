<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PipelineStage>
 */
class PipelineStageFactory extends Factory
{
    إليك كود الـ PipelineStageFactory مجهزاً بالكامل ليتوافق مع جدول مراحل تدفق المبيعات (pipeline_stages).

لقد قمت بتطوير الـ Factory بحيث يولد بيانات منطقية ومتسلسلة تحاكي واقع عمليات المبيعات (Sales Funnel)، مع نسب احتمالية تصاعدية وألوان متناسقة.
PHP

<?php

namespace Database\Factories;

use App\Models\PipelineStage;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PipelineStage>
 */
class PipelineStageFactory extends Factory
{
    /**
     * اسم الموديل المرتبط بالـ Factory
     */
    protected $model = PipelineStage::class;

    /**
     * تعريف البيانات الافتراضية للموديل
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // مصفوفة تعبر عن مراحل المبيعات النموذجية لضمان بيانات اختبار واقعية
        $stages = [
            ['ar' => 'مؤهل جديد', 'en' => 'New Qualified', 'prob' => 10, 'color' => '#3498db'],
            ['ar' => 'تحديد الاحتياجات', 'en' => 'Needs Analysis', 'prob' => 25, 'color' => '#9b59b6'],
            ['ar' => 'تقديم العرض', 'en' => 'Value Proposition', 'prob' => 50, 'color' => '#f1c40f'],
            ['ar' => 'التفاوض المالي', 'en' => 'Negotiation', 'prob' => 80, 'color' => '#e67e22'],
            ['ar' => 'تم الفوز', 'en' => 'Closed Won', 'prob' => 100, 'color' => '#27ae60'],
            ['ar' => 'مفقودة', 'en' => 'Closed Lost', 'prob' => 0, 'color' => '#e74c3c'],
        ];

        $stage = fake()->randomElement($stages);

        return [
            // البيانات الأساسية
            'name'        => $stage['ar'],
            'name_en'     => $stage['en'],
            'code'        => strtoupper(fake()->unique()->lexify('STG-????')),
            
            // الوصف
            'description' => fake('ar_SA')->realText(150),

            // احتمالية الفوز (بناءً على المرحلة المختارة)
            'probability' => $stage['prob'],

            // إعدادات العرض
            'sort_order'  => fake()->numberBetween(1, 100),
            'color'       => $stage['color'],
            
            // محددات الحالة
            'is_won'      => $stage['prob'] === 100,
            'is_lost'     => $stage['prob'] === 0 && $stage['en'] === 'Closed Lost',
            
            // الحالة (95% مفعلة)
            'status'      => fake()->boolean(95) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,

            // التتبع
            'created_by'  => 1,
            'updated_by'  => 1,
        ];
    }
}
