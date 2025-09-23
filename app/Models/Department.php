<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'db_spears';
    protected $table = 'departments';
    public $timestamps = false;

    protected $fillable = [
        'department',
        'section',
    ];
}
