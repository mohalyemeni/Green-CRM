<?php

namespace App\Enums;

enum ActiveStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    /**
     * إرجاع النص العربي لعرضه في الواجهات والقوائم المنسدلة
     */
    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'مفعل',
            self::INACTIVE => 'موقف',
        };
    }

    /**
     * إرجاع كود اللون (مفيد جداً لعمل Badges في الواجهة الأمامية)
     * متوافق مع Bootstrap أو Tailwind CSS
     */
    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success', // لون أخضر
            self::INACTIVE => 'danger', // لون أحمر
        };
    }
}
