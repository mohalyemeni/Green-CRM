<?php

namespace App\Enums;

enum CommentType: int
{
    case COMMENT = 1;
    case QUESTION = 2;
    case ANSWER = 3;
    case INTERNAL = 4;
    case CLOSED = 5;

    /**
     * إرجاع النص العربي لعرضه في الواجهات والقوائم المنسدلة
     */
    public function label(): string
    {
        return match ($this) {
            self::COMMENT => 'تعليق',
            self::QUESTION => 'سؤال',
            self::ANSWER => 'إجابة',
            self::INTERNAL => 'ملاحظة داخلية',
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
            self::COMMENT => 'secondary',   // رمادي
            self::QUESTION => 'warning',    // أصفر
            self::ANSWER => 'info',        // أزرق
            self::INTERNAL => 'dark',      // داكن
            self::CLOSED => 'danger',      // أحمر
        };
    }
}
