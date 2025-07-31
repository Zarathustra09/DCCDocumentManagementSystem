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
        'base_folder_id',
        'filename',
        'original_filename',
        'file_path',
        'file_type',
        'file_size',
        'mime_type',
        'description',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function baseFolder()
    {
        return $this->belongsTo(BaseFolder::class, 'base_folder_id');
    }

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
}
