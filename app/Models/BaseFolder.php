<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseFolder extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function folders()
    {
        return $this->hasMany(Folder::class, 'base_folder_id');
    }

}
