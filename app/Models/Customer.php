<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'code',
        'is_active',
    ];

    public function documentRegistrationEntries()
    {
        return $this->hasMany(DocumentRegistrationEntry::class, 'customer_id');
    }
}
