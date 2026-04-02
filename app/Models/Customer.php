<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\Gender;
use App\Enums\CustomerStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class Customer extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * الحقول المسموح بتعبئتها جماعياً بناءً على ملف الـ Migration
     */
    protected $fillable = [
        // البيانات الأساسية
        'customer_number',
        'name',
        'gender',

        // بيانات التواصل
        'phone',
        'mobile',
        'whatsapp',
        'email',

        // بيانات العنوان
        'general_address',
        'building_number',
        'street_name',
        'district',
        'city',
        'country_id',

        // الحالة والملاحظات
        'status',
        'notes',

        // التتبع والتدقيق
        'created_by',
        'updated_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'status'       => CustomerStatus::class,  // TINYINT → CustomerStatus Enum
        'gender'       => Gender::class,          // TINYINT → Gender Enum
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    /**
     * الوصول لاسم العميل بشكل منسق
     */
    public function getFullNameAttribute(): string
    {
        return ucfirst($this->name);
    }

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة (SearchableTrait)
     */
    protected $searchable = [
        'columns' => [
            'customers.customer_number' => 10,
            'customers.name'            => 10,
            'customers.mobile'          => 9,
            'customers.phone'           => 8,
            'customers.email'           => 8,
            'customers.city'            => 5,
        ],
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
