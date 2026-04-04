<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CrmAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'attachmentable_type',
        'attachmentable_id',
        'customer_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'description',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'file_size'  => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * العلاقة Polymorphic
     */
    public function attachmentable()
    {
        return $this->morphTo();
    }

    /**
     * المستخدم الذي رفع الملف
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العميل المرتبط بالمرفق
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * تنسيق حجم الملف
     */
    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size ?? 0;
        if ($bytes >= 1048576) return round($bytes / 1048576, 1) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    /**
     * أيقونة نوع الملف
     */
    public function getFileIconAttribute(): string
    {
        $type = strtolower($this->file_type ?? '');
        if (str_contains($type, 'pdf')) return 'ri-file-pdf-line text-danger';
        if (str_contains($type, 'image')) return 'ri-image-line text-success';
        if (str_contains($type, 'word') || str_contains($type, 'doc')) return 'ri-file-word-line text-primary';
        if (str_contains($type, 'excel') || str_contains($type, 'sheet')) return 'ri-file-excel-line text-success';
        return 'ri-file-line text-muted';
    }
}
