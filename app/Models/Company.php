<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class Company extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait; // أضفنا SoftDeletes هنا

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'name',
        'name_en',
        'short_name',
        'slug',
        'website',
        'logo',
        'base_currency_id',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'status' => ActiveStatus::class, // ربط حقل الحالة بالـ Enum الخاص بالمشروع
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime', // لضمان تنسيق تاريخ الحذف المنطقي
    ];

    /**
     * إرجاع اسم الشركة مع أول حرف كبير (للاسم الإنجليزي)
     */
    public function getFullNameAttribute(): string
    {
        return ucfirst($this->name);
    }

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة (للبحث السريع)
     */
    protected $searchable = [
        'columns' => [
            // اسم_الجدول.اسم_العمود => الأهمية (رقم)
            'companies.name'       => 10,
            'companies.name_en'    => 10,
            'companies.short_name' => 8,
            'companies.slug'       => 8,
            'companies.website'    => 5,
        ],
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    /**
     * علاقة مع جدول العملات (العملة الأساسية للشركة)
     */
    public function baseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    /**
     * علاقة مع جدول المستخدمين (المُنشئ)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * علاقة مع جدول المستخدمين (المُعدِّل)
     */
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * علاقة مع جدول المستخدمين (الحاذف - في حال الحذف المنطقي)
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
