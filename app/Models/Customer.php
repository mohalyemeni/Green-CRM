<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Enums\Gender;
use App\Enums\CustomerStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class Customer extends Model
{
    use HasFactory, SearchableTrait;
    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'customer_number',
        'name',
        'national_id',
        'age',
        'gender',
        'general_address',
        'building_number',
        'street_name',
        'district',
        'city',
        'country',
        'mobile',
        'email',
        'tax_number',
        'dealing_method',
        'credit_limit',
        'is_active',
        'notes',
        'created_by',
        'updated_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'status' => CustomerStatus::class,      // ربط حقل TINYINT بالـ Enum
        'gender' => Gender::class,      // ربط حقل TINYINT بالـ Enum
        'is_active' => 'boolean',        // تحويل 0 و 1 إلى true/false
        'credit_limit' => 'decimal:2',   // ضمان ظهور الرقم بفاصلة عشرية
        'age' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //get user full name using model
    public function getFullNameAttribute(): string
    {
        return ucfirst($this->name);
    }

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة
     */
    protected $searchable = [
        'columns' => [
            // اسم_الجدول.اسم_العمود => الأهمية (رقم)
            'customers.customer_number' => 10,
            'customers.name' => 10,
            'customers.mobile'  => 10,
            'customers.email'      => 8,
            'customers.status' => 5,
            'customers.country'   => 5,
        ],
    ];

    // علاقة مع جدول المستخدمين (المُنشئ)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // علاقة مع جدول المستخدمين (المُعدِّل)
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
