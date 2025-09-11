<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class CustomerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view customer',
            'create customer',
            'edit customer',
            'delete customer',
        ];

        $roles = ['SuperAdmin', 'DCCAdmin'];

        foreach ($permissions as $permissionName) {
            $permission = Permission::firstOrCreate(['name' => $permissionName]);
            foreach ($roles as $roleName) {
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $roleName]);
                $role->givePermissionTo($permission);
            }
        }

    }
}
