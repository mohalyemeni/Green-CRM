<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\QuotationStatus;
use Nicolaslopezj\Searchable\SearchableTrait;

class Quotation extends Model
{
    use HasFactory, SoftDeletes, SearchableTrait;

    protected $fillable = [
        'code',
        'title',
        'issue_date',
        'expiry_date',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'subtotal',
        'discount_amount',
        'discount_type',
        'tax_amount',
        'total',
        'status',
        'notes',
        'terms_conditions',
        'customer_notes',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejection_reason',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'issue_date'      => 'date',
        'expiry_date'     => 'date',
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'total'           => 'decimal:2',
        'status'          => QuotationStatus::class,
        'approved_at'     => 'datetime',
        'rejected_at'     => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    protected $searchable = [
        'columns' => [
            'quotations.code'             => 10,
            'quotations.title'            => 10,
            'quotations.customer_name'    => 8,
            'quotations.customer_phone'   => 5,
            'quotations.customer_email'   => 5,
        ],
    ];

    // Relations
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function attachments()
    {
        return $this->hasMany(QuotationAttachment::class);
    }

    public function activities()
    {
        return $this->hasMany(QuotationActivity::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
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
