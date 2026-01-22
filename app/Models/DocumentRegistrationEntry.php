<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DocumentRegistrationEntry extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'document_title',
        'document_no',
        'revision_no',
        'device_name',
        'originator_name',
//        'customer',
        'remarks',
        'status_id',
        'submitted_by',
        'implemented_by',
        'submitted_at',
        'implemented_at',
        'rejection_reason',
        'category_id',
        'customer_id',
        'control_no',
        'dcn_no'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'implemented_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function category()
    {
        // This now points to SubCategory model via the subcategories table
        return $this->belongsTo(SubCategory::class, 'category_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function status()
    {
        return $this->belongsTo(DocumentRegistrationEntryStatus::class, 'status_id');
    }

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
        return $this->belongsTo(User::class, 'implemented_by');
    }

    public function files()
    {
        return $this->hasMany(DocumentRegistrationEntryFile::class, 'entry_id');
    }

    public function getFullDocumentNumberAttribute()
    {
        return $this->document_no . ' Rev. ' . $this->revision_no;
    }

    public function getStatusNameAttribute()
    {
        return $this->status->name ?? 'Unknown';
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

    public function scopeCancelled($query)
    {
        return $query->whereHas('status', function ($q) {
            $q->where('name', 'Cancelled');
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->control_no)) {
                $model->control_no = static::generateControlNumber();
            }
        });
    }

    protected static function generateControlNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->format('y');

            $tracker = DB::table('last_control_numbers')
                ->lockForUpdate()
                ->first();

            if (!$tracker) {
                $trackerId = DB::table('last_control_numbers')->insertGetId([
                    'last_number' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $tracker = (object)[
                    'id' => $trackerId,
                    'last_number' => 0,
                ];
            }

            $nextNumber = $tracker->last_number + 1;

            DB::table('last_control_numbers')
                ->where('id', $tracker->id)
                ->update([
                    'last_number' => $nextNumber,
                    'updated_at' => now(),
                ]);

            return sprintf('%s-%04d', $year, $nextNumber);
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
