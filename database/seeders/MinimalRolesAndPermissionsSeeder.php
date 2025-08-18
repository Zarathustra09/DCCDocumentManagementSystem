<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MinimalRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Exclude department permissions

        // Document registration approval permissions
        $documentRegistrationPermissions = [
            'submit document for approval',
            'approve document registration',
            'reject document registration',
            'require revision for document',
            'withdraw document submission',
            'view pending document registrations',
            'view all document registrations',
            'edit document registration details',
            'bulk approve document registrations',
            'bulk reject document registrations',
            'reassign document approver',
            'override approval process',
        ];

        // Folder permissions
        $folderPermissions = [
            'view folders',
            'create folders',
            'edit folders',
            'delete folders',
            'share folders',
        ];

        // System permissions
        $systemPermissions = [
            'view reports',
            'view dashboard',
            'manage users',
            'manage roles',
            'view audit logs',
            'backup system',
            'restore system',
        ];

        // All permissions except department permissions
        $allPermissions = array_merge(
            $documentRegistrationPermissions,
            $folderPermissions,
            $systemPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create SuperAdmin role with all permissions
        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Create DCCAdmin role with all permissions (except department)
        $dccAdminRole = Role::create(['name' => 'DCCAdmin']);
        $dccAdminRole->givePermissionTo($allPermissions);

        // Create example users for each role (local/dev only)
        $this->createExampleUsers();
    }

    /**
     * Create example users for testing purposes
     */
    private function createExampleUsers(): void
    {
        if (!app()->environment(['local', 'development'])) {
            return;
        }

        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@smartprobegroup.com',
                'password' => bcrypt('password'),
                'role' => 'SuperAdmin'
            ],
            [
                'name' => 'DCC Admin',
                'email' => 'dccadmin@smartprobegroup.com',
                'password' => bcrypt('password'),
                'role' => 'DCCAdmin'
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
            ]);
            $user->assignRole($userData['role']);
        }
    }
}
