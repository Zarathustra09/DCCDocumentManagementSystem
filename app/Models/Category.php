<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function documentRegistrationEntries()
    {
        return $this->hasMany(DocumentRegistrationEntry::class, 'category_id');
    }
}
