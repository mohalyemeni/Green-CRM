<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use App\Models\Service;
use Nicolaslopezj\Searchable\SearchableTrait;

class ServiceGroup extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * الحقول المسموح بتعبئتها جماعياً
     * تم التعديل لتشمل الحقول المالية والمتطلبات
     */
    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'requirements',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'status'  => ActiveStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * إعدادات البحث
     */
    protected $searchable = [
        'columns' => [
            'service_groups.name'         => 10,
            'service_groups.description'  => 5,
            'service_groups.requirements' => 5,
        ],
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================
    public function services()
    {
        return $this->hasMany(Service::class, 'service_group_id');
    }
    /**
     * الأب (القسم الرئيسي)
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * الأبناء (الأقسام الفرعية)
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * الأبناء النشطين فقط
     */
    public function appearedChildren()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('status', ActiveStatus::ACTIVE);
    }

    /**
     * علاقة مع المستخدمين
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * جلب الشجرة (Tree)
     */
    public static function tree($level = 1)
    {
        return static::with(implode('.', array_fill(0, $level, 'children')))
            ->whereNull('parent_id')
            ->where('status', ActiveStatus::ACTIVE)
            ->orderBy('id', 'asc')
            ->get();
    }
}
