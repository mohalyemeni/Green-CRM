<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmActivity extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    // As per migration, only created_at is used and timestamps are not managed by Eloquent by default if false,
    // but migration only has created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'activatable_type',
        'activatable_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'user_id',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function activatable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
