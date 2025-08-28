<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentRegistrationEntryFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'status',
        'rejection_reason',
        'implemented_at',
        'implemented_by',
    ];

    protected $casts = [
        'implemented_at' => 'datetime',
    ];

//    TODO: To convert this into a maintenance table
    const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Implemented',
        'rejected' => 'Cancelled',
    ];

    public function registrationEntry()
    {
        return $this->belongsTo(DocumentRegistrationEntry::class, 'entry_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'implemented_by');
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
