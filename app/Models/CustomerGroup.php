<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class CustomerGroup extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * التحميل التلقائي للمودل (Booted)
     */
    protected static function booted()
    {
        static::creating(function ($group) {
            // توليد كود تلقائي إذا لم يتم إدخاله يدوياً
            if (empty($group->code)) {
                $latestGroup = static::withTrashed()->latest('id')->first();
                $nextId = $latestGroup ? $latestGroup->id + 1 : 1;
                $group->code = 'CG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'description',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'status' => ActiveStatus::class, // ربط حقل الحالة بالـ Enum
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة (للبحث السريع)
     */
    protected $searchable = [
        'columns' => [
            // اسم_الجدول.اسم_العمود => الأهمية (رقم 10 هو الأعلى)
            'customer_groups.name'        => 10,
            'customer_groups.name_en'     => 10,
            'customer_groups.code'        => 8,
            'customer_groups.description' => 5,
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

    // ملاحظة: يمكنك مستقبلاً إضافة علاقة مع جدول العملاء هنا 
    // مثلاً: public function customers() { return $this->hasMany(Customer::class); }
}
