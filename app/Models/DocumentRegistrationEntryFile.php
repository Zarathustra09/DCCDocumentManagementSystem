<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentRegistrationEntryFile extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'entry_id',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'status_id',
        'rejection_reason',
        'implemented_at',
        'implemented_by',
    ];

    protected $casts = [
        'implemented_at' => 'datetime',
    ];

    public function status()
    {
        return $this->belongsTo(DocumentRegistrationEntryFileStatus::class, 'status_id');
    }

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
        return $this->status->name ?? 'Unknown';
    }

    public function scopePending($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'Pending');
        });
    }

    public function scopeImplemented($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'Implemented');
        });
    }

    public function scopeReturned($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'Returned');
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
