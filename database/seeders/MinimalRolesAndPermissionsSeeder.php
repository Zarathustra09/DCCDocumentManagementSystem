<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MinimalRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $allPermissions = Permission::pluck('name')->toArray();

        // Create roles
        $superAdminRole = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $superAdminRole->syncPermissions($allPermissions);

        $dccAdminRole = Role::firstOrCreate(['name' => 'DCCAdmin']);
        $dccAdminRole->syncPermissions($allPermissions);

        $basicRole = Role::firstOrCreate(['name' => 'BasicRole']);
        $basicRole->syncPermissions([
            'view folders',
        ]);

        // Assign roles to users
        $superAdminUser = User::where('employee_no', '390')->first();
        if ($superAdminUser) {
            $superAdminUser->assignRole('SuperAdmin');
        }

        $dccAdminUser = User::where('employee_no', '277')->first();
        if ($dccAdminUser) {
            $dccAdminUser->assignRole('DCCAdmin');
        }

        // Assign BasicRole to everyone else
        User::whereNotIn('employee_no', ['390', '277'])->each(function ($user) use ($basicRole) {
            $user->assignRole($basicRole);
        });
    }
}
