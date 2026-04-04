<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmComment extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'commentable_type',
        'commentable_id',
        'body',
        'parent_id',
        'type',
        'is_pinned',
        'is_internal',
        'is_resolved',
        'mentions',
        'user_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_internal' => 'boolean',
        'is_resolved' => 'boolean',
        'mentions' => 'array',
    ];

    public function commentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(CrmComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(CrmComment::class, 'parent_id');
    }
}
