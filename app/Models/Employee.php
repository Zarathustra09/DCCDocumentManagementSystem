<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Employee extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $connection = 'mysql_db2';
    protected $table = 'employees';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'employee_no',
        'username',
        'password',
        'firstname',
        'lastname',
        'email',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Remove the getConnectionName override

    public function roles(): BelongsToMany
    {
        $defaultDb = config('database.connections.mysql.database');
        $employeeDb = config('database.connections.mysql_db2.database');

        return $this->belongsToMany(
            \Spatie\Permission\Models\Role::class,
            $defaultDb . '.model_has_roles',
            'model_id',
            'role_id'
        )->where($defaultDb . '.model_has_roles.model_type', self::class)
         ->select('roles.*');
    }

    public function permissions(): BelongsToMany
    {
        $defaultDb = config('database.connections.mysql.database');
        $employeeDb = config('database.connections.mysql_db2.database');

        return $this->belongsToMany(
            \Spatie\Permission\Models\Permission::class,
            $defaultDb . '.model_has_permissions',
            'model_id',
            'permission_id'
        )->where($defaultDb . '.model_has_permissions.model_type', self::class)
         ->select('permissions.*');
    }

    // Override Spatie methods to use cross-database queries
    public function hasRole($roles, string $guard = null): bool
    {
        return $this->roles()->whereIn('name', is_array($roles) ? $roles : [$roles])->exists();
    }

    public function hasPermissionTo($permission, $guardName = null): bool
    {
        return $this->permissions()->where('name', $permission)->exists() ||
               $this->roles()->whereHas('permissions', function($query) use ($permission) {
                   $query->where('name', $permission);
               })->exists();
    }
}
