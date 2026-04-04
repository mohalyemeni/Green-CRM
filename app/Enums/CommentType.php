<?php

namespace App\Enums;

enum CommentType: int
{
    case NOTE = 1;
    case CALL = 2;
    case MEETING = 3;
    case SYSTEM = 4;
    case CLOSED = 5;

    /**
     * إرجاع النص العربي لعرضه في الواجهات والقوائم المنسدلة
     */
    public function label(): string
    {
        return match ($this) {
            self::NOTE => 'ملاحظة',
            self::CALL => 'مكالمة',
            self::MEETING => 'اجتماع',
            self::SYSTEM => 'نظام',
            self::CLOSED => 'تم الإغلاق',
        };
    }

    /**
     * إرجاع كود اللون (مفيد لعمل Badges في الواجهة)
     * متوافق مع Bootstrap أو Tailwind
     */
    public function color(): string
    {
        return match ($this) {
            self::NOTE => 'secondary',
            self::CALL => 'primary',
            self::MEETING => 'success',
            self::SYSTEM => 'dark',
            self::CLOSED => 'danger',
        };
    }
}
