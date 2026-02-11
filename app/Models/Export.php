<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Export extends Model
{
    protected $fillable = [
        'employee_no',
        'control_no',
        'file_name',
        'disk',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($export) {
            if (empty($export->control_no)) {
                do {
                    $controlNo = 'EXP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
                } while (self::where('control_no', $controlNo)->exists());

                $export->control_no = $controlNo;
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_no', 'employee_no');
    }
}
