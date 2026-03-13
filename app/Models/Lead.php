<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class Lead extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    /**
     * التحميل التلقائي للمودل (Booted)
     * لتوليد رقم العميل المحتمل تلقائياً عند الإنشاء
     */
    protected static function booted()
    {
        static::creating(function ($lead) {
            if (empty($lead->lead_number)) {
                $latestLead = static::withTrashed()->latest('id')->first();
                $nextId = $latestLead ? $latestLead->id + 1 : 1;
                $lead->lead_number = 'LE-' . date('Y') . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'lead_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'mobile',
        'other_mobile',
        'job_title',
        'company_name',
        'website',
        'company_id',
        'branch_id',
        'lead_status_id',
        'lead_source_id',
        'industry_id',
        'owner_id',
        'country_id',
        'state',
        'city',
        'address',
        'currency_id',
        'estimated_value',
        'priority',
        'rating',
        'description',
        'notes',
        'last_contacted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * تحويل أنواع البيانات (Casting)
     */
    protected $casts = [
        'estimated_value'   => 'decimal:2',
        'priority'          => 'integer', // 1: High, 2: Medium, 3: Low
        'rating'            => 'integer',
        'last_contacted_at' => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    /**
     * إعدادات البحث وتحديد أولويات الأعمدة
     * تم ضبط الأولوية لبيانات الهوية (الاسم، الجوال، البريد)
     */
    protected $searchable = [
        'columns' => [
            'leads.first_name'   => 10,
            'leads.last_name'    => 10,
            'leads.mobile'       => 10,
            'leads.email'        => 8,
            'leads.lead_number'  => 8,
            'leads.company_name' => 5,
        ],
    ];

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    /**
     * العلاقة مع الحالة (Lead Status)
     */
    public function status()
    {
        return $this->belongsTo(LeadStatus::class, 'lead_status_id');
    }

    /**
     * العلاقة مع المصدر (Lead Source)
     */
    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'lead_source_id');
    }

    /**
     * العلاقة مع القطاع (Industry)
     */
    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    /**
     * الموظف المسؤول عن العميل (Owner)
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * الدولة التابع لها العميل
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * العملة المستخدمة في تقييم العميل
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
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

    // ==========================================
    // الصفات المشتقة (Accessors)
    // ==========================================

    /**
     * الحصول على الاسم الكامل
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
