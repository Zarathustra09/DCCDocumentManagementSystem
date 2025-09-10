<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Spatie\Permission\Models\Permission;

class CategoryPermissionSeeder extends Seeder
{

    public function run(): void
    {
      $permissions = [
          'view category',
          'create category',
          'edit category',
          'delete category',
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
