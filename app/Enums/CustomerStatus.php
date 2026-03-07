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
            self::ACTIVE => 'مفعل',
            self::INACTIVE => 'غير مفعل',
            self::SUSPENDED => 'موقوف مؤقتاً',
        };
    }
}
