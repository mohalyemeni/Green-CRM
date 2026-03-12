<?php

namespace App\Enums;

enum OpportunityStatus: int
{
    case OPEN = 1;
    case WON = 2;
    case LOST = 3;
    case ON_HOLD = 4;

    // دالة مساعدة لترجمة الحالات للواجهة
    public function label(): string
    {
        return match ($this) {
            self::OPEN => 'مفتوحة',
            self::WON => 'فوز',
            self::LOST => 'خسارة',
            self::ON_HOLD => 'قيد الانتظار',
        };
    }

    // دالة مساعدة لإرجاع لون الحالة (للاستخدام في CSS Classes)
    public function color(): string
    {
        return match ($this) {
            self::OPEN => 'primary', // أزرق (للدلالة على أن الفرصة نشطة)
            self::WON => 'success', // أخضر (للدلالة على النجاح والفوز)
            self::LOST => 'danger', // أحمر (للدلالة على الخسارة)
            self::ON_HOLD => 'warning', // أصفر/برتقالي (للدلالة على التوقف المؤقت)
        };
    }

    // يمكنك أيضاً إضافة دالة لإرجاع كود اللون (Hex) إذا كنت لا تستخدم Bootstrap/Tailwind
    public function hexColor(): string
    {
        return match ($this) {
            self::OPEN => '#0d6efd',
            self::WON => '#198754',
            self::LOST => '#dc3545',
            self::ON_HOLD => '#ffc107',
        };
    }
}
