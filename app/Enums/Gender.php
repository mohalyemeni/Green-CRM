<?php

namespace App\Enums;

enum Gender: int
{
    case MALE = 1;
    case FEMALE = 2;

    // دالة مساعدة لإرجاع النص باللغة المطلوبة
    public function label(): string
    {
        return match ($this) {
            self::MALE => __('Male') ?? 'ذكر',
            self::FEMALE => __('Female') ?? 'انثى',
        };
    }

    // دالة اختيارية لإرجاع الألوان (مثلاً للـ Badges في الواجهة)
    public function color(): string
    {
        return match ($this) {
            self::MALE => 'blue',
            self::FEMALE => 'pink',
        };
    }
}
