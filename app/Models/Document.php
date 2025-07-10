<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'folder_id',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'description',
        'meta_data',
        'department',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    // Use same departments as Folder model
    const DEPARTMENTS = [
        'IT' => 'IT Department',
        'Finance' => 'Finance Department',
        'QA' => 'QA Department',
        'HR' => 'HR Department',
        'Purchasing' => 'Purchasing Department',
        'Sales' => 'Sales Department',
        'Operations' => 'Operations Department',
        'General' => 'General/Public'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function getDepartmentNameAttribute()
    {
        return self::DEPARTMENTS[$this->department] ?? $this->department;
    }

    public function scopeForDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    public function scopeAccessibleByUser($query, $user)
    {
        $accessibleDepartments = [];

        foreach (self::DEPARTMENTS as $dept => $name) {
            if ($user->can("view {$dept} documents")) {
                $accessibleDepartments[] = $dept;
            }
        }

        return $query->whereIn('department', $accessibleDepartments);
    }
}
