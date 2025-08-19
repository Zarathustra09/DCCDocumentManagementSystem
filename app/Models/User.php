<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'employee_no',
        'username',
        'password',
        'firstname',
        'middlename',
        'lastname',
        'address',
        'birthdate',
        'contact_info',
        'gender',
        'datehired',
        'profile_image',
        'created_on',
        'barcode',
        'email',
        'separationdate',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'datehired' => 'date',
            'created_on' => 'date',
            'separationdate' => 'date',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute()
    {
        return trim("{$this->firstname} {$this->middlename} {$this->lastname}");
    }
}
