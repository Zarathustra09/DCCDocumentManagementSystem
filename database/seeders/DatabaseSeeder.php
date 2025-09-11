<?php

namespace Database\Seeders;

use App\Http\Controllers\PermissionController;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Whoops\Run;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
      $this->call([
//            RolesAndPermissionsSeeder::class,
//            FolderSeeder::class,
//          PermissionSeeder::class,
//          RunSqlSeeder::class,
//          MinimalRolesAndPermissionsSeeder::class,
//      CategorySeeder::class
      RemoveMechatronicsParentCategorySeeder::class,
      CustomerSeeder::class,
       CustomerPermissionSeeder::class,

        ]);
    }
}
