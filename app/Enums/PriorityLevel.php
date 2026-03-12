<?php

namespace App\Enums;

enum PriorityLevel: int
{
    case LOW = 1;
    case MEDIUM = 2;
    case HIGH = 3;
    case URGENT = 4;

    /**
     * إرجاع النص العربي لعرضه في الواجهات
     */
    public function label(): string
    {
        return match ($this) {
            self::LOW => 'منخفضة',
            self::MEDIUM => 'متوسطة',
            self::HIGH => 'عالية',
            self::URGENT => 'عاجلة',
        };
    }

    /**
     * إرجاع لون الحالة (للاستخدام في Bootstrap / Tailwind CSS Classes)
     */
    public function color(): string
    {
        return match ($this) {
            self::LOW => 'info', // أزرق فاتح (للدلالة على أولوية عادية/منخفضة)
            self::MEDIUM => 'primary', // أزرق أساسي (للدلالة على أولوية قياسية)
            self::HIGH => 'warning', // برتقالي/أصفر (للدلالة على الانتباه/عالية)
            self::URGENT => 'danger', // أحمر (للدلالة على الخطر/عاجلة جداً)
        };
    }

    /**
     * إرجاع كود اللون (Hex) إذا كنت تستخدم تصميمات مخصصة
     */
    public function hexColor(): string
    {
        return match ($this) {
            self::LOW => '#0dcaf0',
            self::MEDIUM => '#0d6efd',
            self::HIGH => '#ffc107',
            self::URGENT => '#dc3545',
        };
    }
}
