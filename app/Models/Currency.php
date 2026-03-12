<?php

namespace App\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nicolaslopezj\Searchable\SearchableTrait;

class Currency extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'fraction_name',
        'exchange_rate',
        'equivalent',
        'max_exchange_rate',
        'min_exchange_rate',
        'is_local',
        'is_inventory',
        'status',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'exchange_rate'     => 'decimal:6',
        'equivalent'        => 'decimal:6',
        'max_exchange_rate' => 'decimal:6',
        'min_exchange_rate' => 'decimal:6',
        'is_local'          => 'boolean',
        'is_inventory'      => 'boolean',
        'status'            => ActiveStatus::class,
    ];

    protected $searchable = [
        'columns' => [
            'currencies.name' => 10,
            'currencies.code' => 10,
            'currencies.symbol' => 8,
        ],
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
