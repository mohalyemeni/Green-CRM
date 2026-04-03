<?php

namespace App\Enums;

enum QuotationStatus: string
{
    case DRAFT = 'draft';           // مسودة
    case PENDING = 'pending';       // قيد المراجعة
    case APPROVED = 'approved';     // معتمد
    case SENT = 'sent';             // مُرسل للعميل
    case ACCEPTED = 'accepted';     // مقبول من العميل
    case REJECTED = 'rejected';     // مرفوض من العميل
    case EXPIRED = 'expired';       // منتهي الصلاحية
    case CONVERTED = 'converted';   // تم تحويله لفاتورة/عقد

    // دالة لإرجاع النص المترجم أو الافتراضي بالعربية
    public function label(): string
    {
        return match ($this) {
            self::DRAFT => __('Draft') ?? 'مسودة',
            self::PENDING => __('Pending Review') ?? 'قيد المراجعة',
            self::APPROVED => __('Approved') ?? 'معتمد',
            self::SENT => __('Sent') ?? 'مُرسل للعميل',
            self::ACCEPTED => __('Accepted') ?? 'مقبول',
            self::REJECTED => __('Rejected') ?? 'مرفوض',
            self::EXPIRED => __('Expired') ?? 'منتهي الصلاحية',
            self::CONVERTED => __('Converted') ?? 'تم التحويل',
        };
    }

    // دالة لإرجاع الألوان المناسبة للواجهات (Bootstrap)
    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',    // رمادي
            self::PENDING => 'warning',     // برتقالي/أصفر
            self::APPROVED => 'info',        // أزرق سماوي
            self::SENT => 'primary',       // أزرق
            self::ACCEPTED => 'success',    // أخضر
            self::REJECTED => 'danger',     // أحمر
            self::EXPIRED => 'dark',        // أسود/رمادي غامق
            self::CONVERTED => 'success',   // أخضر (لأنها حالة إيجابية نهائية)
        };
    }

    // دالة لإرجاع الأيقونة المناسبة (Remix Icon أو FontAwesome)
    public function icon(): string
    {
        return match ($this) {
            self::DRAFT => 'ri-file-edit-line',
            self::PENDING => 'ri-time-line',
            self::APPROVED => 'ri-checkbox-circle-line',
            self::SENT => 'ri-mail-send-line',
            self::ACCEPTED => 'ri-emotion-happy-line',
            self::REJECTED => 'ri-close-circle-line',
            self::EXPIRED => 'ri-error-warning-line',
            self::CONVERTED => 'ri-exchange-dollar-line',
        };
    }
}
