<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class PipelineStage extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'name',
        'name_en',
        'code',
        'description',
        'probability',
        'sort_order',
        'color',
        'is_won',
        'is_lost',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'status'      => ActiveStatus::class, // ربط حقل الحالة بالـ Enum
        'probability' => 'decimal:2',
        'sort_order'  => 'integer',
        'is_won'      => 'boolean',
        'is_lost'     => 'boolean',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة (للبحث السريع)
     */
    protected $searchable = [
        'columns' => [
            // اسم_الجدول.اسم_العمود => الأهمية (رقم)
            'pipeline_stages.name'        => 10,
            'pipeline_stages.name_en'     => 10,
            'pipeline_stages.code'        => 8,
            'pipeline_stages.description' => 5,
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
     * علاقة مع جدول المستخدمين (الحاذف)
     */
    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }
}
