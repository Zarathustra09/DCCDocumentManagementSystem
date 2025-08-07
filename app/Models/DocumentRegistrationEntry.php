<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentRegistrationEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_title',
        'document_no',
        'revision_no',
        'device_name',
        'originator_name',
        'customer',
        'remarks',
        'status',
        'submitted_by',
        'approved_by',
        'submitted_at',
        'approved_at',
//        'rejection_reason',
//        'revision_notes',
//        'file_path',
//        'original_filename',
//        'mime_type',
//        'file_size',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];
    //TODO: to add edit functionality for documents
    const STATUSES = [
        'pending' => 'Pending Registration',
        'approved' => 'Implemented',
        'rejected' => 'Cancelled',
    ];

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function getFullDocumentNumberAttribute()
    {
        return $this->document_no . ' Rev. ' . $this->revision_no;
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) return null;

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function hasFile()
    {
        return $this->file_path && Storage::disk('local')->exists($this->file_path);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
    public function files()
    {
        return $this->hasMany(DocumentRegistrationEntryFile::class, 'entry_id');
    }
}
