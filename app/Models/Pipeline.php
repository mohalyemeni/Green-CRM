<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use App\Traits\HasActiveScope;
use Nicolaslopezj\Searchable\SearchableTrait;

class Pipeline extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'name',
        'name_en',
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
        'status' => ActiveStatus::class,
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * إعدادات البحث
     */
    protected $searchable = [
        'columns' => [
            'pipelines.name'    => 10,
            'pipelines.name_en' => 10,
        ],
    ];

    /**
     * المراحل التابعة لهذا القمع
     */
    public function stages()
    {
        return $this->hasMany(PipelineStage::class)->orderBy('sort_order');
    }

    /**
     * العلاقات الأساسية للمستخدمين
     */
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function editor() { return $this->belongsTo(User::class, 'updated_by'); }
    public function deleter() { return $this->belongsTo(User::class, 'deleted_by'); }
}
