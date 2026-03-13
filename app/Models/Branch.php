<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Branch extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait, HasSlug;

    /**
     * الحقول المسموح بتعبئتها جماعياً
     */
    protected $fillable = [
        'company_id',
        'code',
        'name',
        'name_en',
        'slug',
        'commercial_register',
        'tax_number',
        'country_id',
        'state',
        'city',
        'district',
        'building_number',
        'street_address',
        'postal_code',
        'po_box',
        'timezone',
        'currency_id',
        'phone',
        'mobile',
        'email',
        'fax',
        'logo',
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
     * إعدادات البحث وتحديد أولويات الأعمدة (للبحث السريع في الفروع)
     */
    protected $searchable = [
        'columns' => [
            // اسم_الجدول.اسم_العمود => الأهمية (رقم)
            'branches.name'                => 10,
            'branches.name_en'             => 10,
            'branches.code'                => 10,
            'branches.commercial_register' => 8,
            'branches.tax_number'          => 8,
            'branches.mobile'              => 5,
            'branches.email'               => 5,
        ],
    ];

    // ==========================================
    // Accessors (خصائص إضافية مجمعة)
    // ==========================================

    /**
     * إرجاع العنوان الكامل للفرع كنص واحد (مفيد جداً في طباعة الفواتير)
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->street_address,
            $this->district,
            $this->city,
            $this->state
        ]);

        return implode(' - ', $parts);
    }

    // ==========================================
    // العلاقات (Relationships)
    // ==========================================

    /**
     * علاقة مع جدول الشركات (الشركة الأم التي يتبع لها هذا الفرع)
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * علاقة مع جدول الدول (دولة الفرع)
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    /**
     * علاقة مع جدول العملات (العملة الافتراضية للفرع)
     */
    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

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

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->usingLanguage('ar'); // Use Arabic for slug generation if needed, or leave default
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($branch) {
            if (empty($branch->code)) {
                $latestBranch = static::orderBy('id', 'desc')->first();
                $lastId = $latestBranch ? $latestBranch->id : 0;
                $branch->code = 'BR-' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
