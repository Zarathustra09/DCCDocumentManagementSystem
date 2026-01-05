<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $connection = 'db_spears';
    protected $table = 'organizations';
    public $timestamps = false;

    protected $fillable = [
        'organization',
        'orgcode',
    ];
}
