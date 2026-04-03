<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationActivity extends Model
{
    use HasFactory;

    // تعطيل updated_at لأن السجل للقراءة فقط عادة (Log)
    public const UPDATED_AT = null;

    protected $fillable = [
        'quotation_id',
        'action',
        'description',
        'old_values',
        'new_values',
        'user_id',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
