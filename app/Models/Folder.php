<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
//        'department',
        'description',
    ];

//     const DEPARTMENTS = [
//         'IT' => 'IT Department',
//         'Finance' => 'Finance Department',
//         'QA' => 'QA Department',
//         'HR' => 'HR Department',
//         'Purchasing' => 'Purchasing Department',
//         'Sales' => 'Sales Department',
//         'Operations' => 'Operations Department',
//         'General' => 'General/Public',
//         'Business Unit 1' => 'Business Unit 1',
//         'Business Unit 2' => 'Business Unit 2',
//         'Business Unit 3' => 'Business Unit 3'
//     ];

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

//    public function getDepartmentNameAttribute()
//    {
//        return self::DEPARTMENTS[$this->department] ?? $this->department;
//    }

//    public function scopeForDepartment($query, $department)
//    {
//        return $query->where('department', $department);
//    }
//
  public function scopeAccessibleByUser($query, $user)
  {
      $accessibleBaseFolders = [];

      foreach (BaseFolder::all() as $baseFolder) {
          if ($user->can("view {$baseFolder->name} documents")) {
              $accessibleBaseFolders[] = $baseFolder->id;
          }
      }

      return $query->whereIn('base_folder_id', $accessibleBaseFolders);
  }

    public function baseFolder()
    {
        return $this->belongsTo(BaseFolder::class, 'base_folder_id');
    }
}
