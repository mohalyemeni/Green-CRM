<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class Country extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'name',
        'name_en',
        'country_code',
        'phone_code',
        'nationality',
        'nationality_en',
        'status',
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
     * إعدادات البحث وتحديد أولويات الأعمدة (للبحث السريع)
     */
    protected $searchable = [
        'columns' => [
            // اسم_الجدول.اسم_العمود => الأهمية (رقم)
            'countries.name'           => 10,
            'countries.name_en'        => 10,
            'countries.country_code'   => 8,
            'countries.phone_code'     => 8,
            'countries.nationality'    => 5,
            'countries.nationality_en' => 5,
        ],
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

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
