<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\ActiveStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class Service extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    protected $fillable = [
        'service_group_id',
        'name',
        'description',
        'requirements',
        'price',
        'cost',
        'taxable',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'status'     => ActiveStatus::class,
        'price'      => 'decimal:2',
        'cost'       => 'decimal:2',
        'taxable'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $searchable = [
        'columns' => [
            'services.name'         => 10,
            'services.description'  => 5,
            'services.requirements' => 5,
        ],
    ];

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
