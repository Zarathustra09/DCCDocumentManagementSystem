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
               'firstname' => 'Super',
               'middlename' => 'Admin',
               'lastname' => 'User',
               'employee_no' => 'EMP001',
               'username' => 'superadmin',
               'address' => '123 Admin St',
               'birthdate' => '1990-01-01',
               'contact_info' => '09171234567',
               'gender' => 'Male',
               'datehired' => '2020-01-01',
               'profile_image' => '', // or a default image path
               'created_on' => now()->toDateString(),
               'barcode' => 'BARCODE001',
               'email' => 'superadmin@smartprobegroup.com',
               'separationdate' => null,
               'password' => bcrypt('password'),
               'email_verified_at' => now(),
               'remember_token' => \Str::random(10),
               'role' => 'SuperAdmin'
           ],
           [
               'firstname' => 'DCC',
               'middlename' => 'Admin',
               'lastname' => 'User',
               'employee_no' => 'EMP002',
               'username' => 'dccadmin',
               'address' => '456 DCC St',
               'birthdate' => '1992-02-02',
               'contact_info' => '09179876543',
               'gender' => 'Female',
               'datehired' => '2021-02-02',
               'profile_image' => '',
               'created_on' => now()->toDateString(),
               'barcode' => 'BARCODE002',
               'email' => 'dccadmin@smartprobegroup.com',
               'separationdate' => null,
               'password' => bcrypt('password'),
               'email_verified_at' => now(),
               'remember_token' => \Str::random(10),
               'role' => 'DCCAdmin'
           ],
       ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);
            $user = \App\Models\User::create($userData);
            $user->assignRole($role);
        }
    }
}
