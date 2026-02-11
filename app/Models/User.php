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
        'organization_id',
        'department_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'birthdate' => 'datetime',
        'datehired' => 'datetime',
        'created_on' => 'datetime',
        'separationdate' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     public function getNameAttribute()
     {
         return trim("{$this->firstname} {$this->lastname}");
     }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function export()
    {
        return $this->hasMany(Export::class, 'employee_no', 'employee_no');
    }

}
