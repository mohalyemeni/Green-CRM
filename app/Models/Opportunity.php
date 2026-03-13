<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Opportunity extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * التحميل التلقائي للمودل (Booted)
     * لتوليد رقم الفرصة البيعية تلقائياً عند الإنشاء
     */
    protected static function booted()
    {
        static::creating(function ($opportunity) {
            if (empty($opportunity->opportunity_number)) {
                $latestOpp = static::withTrashed()->latest('id')->first();
                $nextId = $latestOpp ? $latestOpp->id + 1 : 1;
                // توليد كود مثل: OPP-202400001
                $opportunity->opportunity_number = 'OPP-' . date('Y') . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'title',
        'description',
        'opportunity_number',
        'company_id',
        'branch_id',
        'customer_id',
        'pipeline_id',
        'stage_id',
        'opportunity_source_id',
        'opportunity_type',
        'currency_id',
        'expected_revenue',
        'probability',
        'expected_close_date',
        'closed_at',
        'priority',
        'lost_reason_id',
        'lost_reason_notes',
        'assigned_to',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'expected_revenue'    => 'decimal:2',
        'probability'         => 'integer',
        'expected_close_date' => 'date',
        'closed_at'           => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة
     * تم ضبط الأولوية لعنوان الفرصة ورقمها المرجعي
     */
    protected $searchable = [
        'columns' => [
            'opportunities.title'              => 10,
            'opportunities.opportunity_number' => 10,
            'opportunities.opportunity_type'   => 8,
            'opportunities.description'        => 5,
        ],
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    /**
     * العميل المرتبط بالفرصة البيعية
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * الشركة والفرع الداخلي
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    /**
     * قمع المبيعات والمرحلة الحالية
     */
    public function pipeline()
    {
        return $this->belongsTo(Pipeline::class, 'pipeline_id');
    }

    public function stage()
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }

    /**
     * مصدر الفرصة
     */
    public function source()
    {
        return $this->belongsTo(OpportunitySource::class, 'opportunity_source_id');
    }

    /**
     * العملة المستخدمة في تقييم الفرصة
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    /**
     * سبب خسارة الفرصة (في حال تم إغلاقها كخسارة)
     */
    public function lostReason()
    {
        return $this->belongsTo(LostReason::class, 'lost_reason_id');
    }

    /**
     * الموظف المسؤول عن الفرصة
     */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * تتبع السجلات (Audit Trail)
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
}
