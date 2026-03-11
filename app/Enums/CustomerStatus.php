<?php

namespace App\Enums;

enum CustomerStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case SUSPENDED = 3;

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'مفعّل',
            self::INACTIVE => 'غير مفعّل',
            self::SUSPENDED => 'معلّق',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',   // سيظهر باللون الأخضر في Velzon
            self::INACTIVE => 'danger',  // سيظهر باللون الأحمر
            self::SUSPENDED => 'warning', // سيظهر باللون البرتقالي
        };
    }
}
