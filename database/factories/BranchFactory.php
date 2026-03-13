<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Branch>
 */
class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // العلاقات (بافتراض أن لديك شركات، دول، وعملات مسجلة مسبقاً بأرقام من 1 إلى 5)
            'company_id'          => fake()->numberBetween(1, 5),
            'country_id'          => fake()->numberBetween(1, 5),
            'currency_id'         => fake()->numberBetween(1, 5),

            // البيانات الأساسية للفرع
            // توليد كود فرع مميز (مثال: BR-1042)
            'code'                => fake()->unique()->bothify('BR-####'),
            // أسماء الفروع غالباً ما ترتبط بأسماء المناطق أو الشوارع
            'name'                => 'فرع ' . fake('ar_SA')->streetName(),
            'name_en'             => fake('en_US')->streetName() . ' Branch',
            'slug'                => fake()->unique()->slug(2),

            // البيانات القانونية والضريبية
            // صيغة مقاربة للسجل التجاري السعودي (10 أرقام تبدأ بـ 1010)
            'commercial_register' => fake()->optional(0.8)->numerify('1010######'),
            // صيغة مقاربة للرقم الضريبي (15 رقم يبدأ بـ 3 وينتهي بـ 3)
            'tax_number'          => fake()->optional(0.8)->numerify('300########0003'),

            // بيانات العنوان والموقع
            'state'               => fake('ar_SA')->state(), // المنطقة/المحافظة
            'city'                => fake('ar_SA')->city(),  // المدينة
            'district'            => fake('ar_SA')->streetName(), // الحي
            'building_number'     => fake()->buildingNumber(),
            'street_address'      => fake('ar_SA')->streetAddress(),
            'postal_code'         => fake()->postcode(),
            'po_box'              => fake()->optional()->numerify('#####'), // صندوق بريد (أحياناً null)

            // الإعدادات الخاصة
            'timezone'            => fake()->timezone(),

            // بيانات التواصل (استخدمنا optional لجعل بعض الحقول null واقعياً)
            'phone'               => fake()->optional()->phoneNumber(),
            'mobile'              => fake()->phoneNumber(),
            'email'               => fake()->unique()->safeEmail(),
            'fax'                 => fake()->optional()->phoneNumber(),

            // الشعار والحالة
            'logo'                => fake()->optional()->imageUrl(200, 200, 'business', true, 'Branch'),
            // احتمال 90% أن يكون الفرع مفعل (1) و 10% أن يكون موقوف (0)
            'status'              => fake()->boolean(90) ? ActiveStatus::ACTIVE->value : ActiveStatus::INACTIVE->value,

            // التتبع
            'created_by'          => 1,
            'updated_by'          => 1,
        ];
    }
}
