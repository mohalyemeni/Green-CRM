<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class OpportunitySource extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * التحميل التلقائي للمودل (Booted)
     */
    protected static function booted()
    {
        static::creating(function ($source) {
            // توليد كود تلقائي إذا لم يتم إدخاله يدوياً
            if (empty($source->code)) {
                $latestSource = static::withTrashed()->latest('id')->first();
                $nextId = $latestSource ? $latestSource->id + 1 : 1;
                $source->code = 'OS-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
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
        'color',
        'icon',
        'status',
        'sort_order',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'status' => ActiveStatus::class, // ربط حقل الحالة بالـ Enum الخاص بالمشروع
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة
     */
    protected $searchable = [
        'columns' => [
            // اسم_الجدول.اسم_العمود => الأهمية
            'opportunity_sources.name'        => 10,
            'opportunity_sources.name_en'     => 10,
            'opportunity_sources.code'        => 8,
            'opportunity_sources.description' => 5,
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
