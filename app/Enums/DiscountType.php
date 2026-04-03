<?php

namespace App\Enums;

enum DiscountType: string
{
    case AMOUNT = 'amount';
    case PERCENTAGE = 'percentage';

    // دالة لإرجاع النص المترجم أو الافتراضي بالعربية
    public function label(): string
    {
        return match ($this) {
            self::AMOUNT => __('Fixed Amount') ?? 'مبلغ ثابت',
            self::PERCENTAGE => __('Percentage') ?? 'نسبة مئوية',
        };
    }

    // دالة لإرجاع الألوان المناسبة للواجهات (Bootstrap أو Tailwind)
    public function color(): string
    {
        return match ($this) {
            self::AMOUNT => 'success',    // أخضر للمبالغ الثابتة مثلاً
            self::PERCENTAGE => 'primary', // أزرق للنسب المئوية
        };
    }

    // دالة إضافية لإرجاع الرمز (مثلاً للـ Input Addon)
    public function symbol(): string
    {
        return match ($this) {
            self::AMOUNT => '$',
            self::PERCENTAGE => '%',
        };
    }
}
