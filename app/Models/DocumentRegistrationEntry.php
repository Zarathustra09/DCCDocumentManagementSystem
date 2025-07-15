<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'rejection_reason',
        'revision_notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    const STATUSES = [
        'pending' => 'Pending Approval',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
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
}
