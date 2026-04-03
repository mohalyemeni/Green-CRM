<?php

namespace App\Models;

use App\Helper\MySlugHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Service extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait, HasSlug;

    protected $fillable = [
        'service_group_id',
        'code',
        'name',
        'slug',
        'description',
        'requirements',
        'base_cost',
        'price',
        'min_price',
        'max_discount',
        'discount_type',
        'status',
        'is_taxable',
        'tax_rate',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'status'         => ActiveStatus::class,
        'discount_type'  => \App\Enums\DiscountType::class,
        'base_cost'      => 'decimal:2',
        'price'          => 'decimal:2',
        'min_price'      => 'decimal:2',
        'max_discount'   => 'decimal:2',
        'tax_rate'       => 'decimal:2',
        'is_taxable'     => 'boolean',
        'created_at'     => 'datetime',
        'updated_at'     => 'datetime',
        'deleted_at'     => 'datetime',
    ];

    protected $searchable = [
        'columns' => [
            'services.code'         => 10,
            'services.name'         => 10,
            'services.slug'         => 8,
            'services.description'  => 5,
            'services.requirements' => 5,
        ],
    ];
    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    protected function generateNonUniqueSlug(): string
    {
        $slugField = $this->slugOptions->slugField;

        if ($this->hasCustomSlugBeenUsed() && ! empty($this->$slugField)) {
            return $this->$slugField;
        }

        return MySlugHelper::slug($this->getSlugSourceString());

        // return Str::slug($this->getSlugSourceString(), $this->slugOptions->slugSeparator, $this->slugOptions->slugLanguage);
    }
    public function serviceGroup()
    {
        return $this->belongsTo(ServiceGroup::class, 'service_group_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
