<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
        'department',
        'description',
    ];

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

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
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
