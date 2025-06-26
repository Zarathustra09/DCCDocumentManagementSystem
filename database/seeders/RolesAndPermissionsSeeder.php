<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create document permissions
        $documentPermissions = [
            'view documents',
            'create documents',
            'edit documents',
            'delete documents',
            'download documents',
            'share documents',
        ];

        // Create folder permissions
        $folderPermissions = [
            'view folders',
            'create folders',
            'edit folders',
            'delete folders',
            'share folders',
        ];

        // Create system permissions
        $systemPermissions = [
            'manage users',
            'manage roles',
            'view reports',
            'access settings',
        ];

        // Create all permissions
        $allPermissions = array_merge($documentPermissions, $folderPermissions, $systemPermissions);
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create admin role with all permissions
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Create manager role with specific permissions
        $managerRole = Role::create(['name' => 'manager']);
        $managerRole->givePermissionTo([
            'view documents',
            'create documents',
            'edit documents',
            'delete documents',
            'download documents',
            'share documents',
            'view folders',
            'create folders',
            'edit folders',
            'delete folders',
            'share folders',
            'view reports',
        ]);

        // Create editor role
        $editorRole = Role::create(['name' => 'editor']);
        $editorRole->givePermissionTo([
            'view documents',
            'create documents',
            'edit documents',
            'download documents',
            'view folders',
            'create folders',
            'edit folders',
        ]);

        // Create viewer role
        $viewerRole = Role::create(['name' => 'viewer']);
        $viewerRole->givePermissionTo([
            'view documents',
//            'download documents',
            'view folders',
        ]);

        // Assign admin role to first user (if exists)
        $adminUser = User::first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }

        // Create example users for each role
        $this->createExampleUsers();
    }

    /**
     * Create example users for testing purposes
     */
    private function createExampleUsers(): void
    {
        // Skip if not in local or development environment
        if (!app()->environment(['local', 'development'])) {
            return;
        }

        // Create admin user if doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('admin');

        // Create manager user
        $manager = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            [
                'name' => 'Manager User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->assignRole('manager');

        // Create editor user
        $editor = User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name' => 'Editor User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $editor->assignRole('editor');

        // Create viewer user
        $viewer = User::firstOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name' => 'Viewer User',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]
        );
        $viewer->assignRole('viewer');
    }
}
