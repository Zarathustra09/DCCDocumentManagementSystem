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

        // Create department-specific document permissions
        $departmentPermissions = [
            // IT Department
            'view IT documents',
            'create IT documents',
            'edit IT documents',
            'delete IT documents',
            'download IT documents',
            'share IT documents',

            // Finance/Accounting
            'view Finance documents',
            'create Finance documents',
            'edit Finance documents',
            'delete Finance documents',
            'download Finance documents',
            'share Finance documents',

            // QA Department
            'view QA documents',
            'create QA documents',
            'edit QA documents',
            'delete QA documents',
            'download QA documents',
            'share QA documents',

            // HR Department
            'view HR documents',
            'create HR documents',
            'edit HR documents',
            'delete HR documents',
            'download HR documents',
            'share HR documents',

            // Purchasing Department
            'view Purchasing documents',
            'create Purchasing documents',
            'edit Purchasing documents',
            'delete Purchasing documents',
            'download Purchasing documents',
            'share Purchasing documents',

            // Sales Department
            'view Sales documents',
            'create Sales documents',
            'edit Sales documents',
            'delete Sales documents',
            'download Sales documents',
            'share Sales documents',

            // Operations Department
            'view Operations documents',
            'create Operations documents',
            'edit Operations documents',
            'delete Operations documents',
            'download Operations documents',
            'share Operations documents',

            // General/Public documents
            'view General documents',
            'create General documents',
            'edit General documents',
            'delete General documents',
            'download General documents',
            'share General documents',
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
            'system backup',
            'system restore',
        ];

        // Create all permissions
        $allPermissions = array_merge($departmentPermissions, $folderPermissions, $systemPermissions);
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create SuperAdmin role with all permissions
        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Create DCCAdmin role with administrative permissions
        $dccAdminRole = Role::create(['name' => 'DCCAdmin']);
        $dccAdminRole->givePermissionTo([
            // All department documents
            'view IT documents', 'create IT documents', 'edit IT documents', 'delete IT documents', 'download IT documents', 'share IT documents',
            'view Finance documents', 'create Finance documents', 'edit Finance documents', 'delete Finance documents', 'download Finance documents', 'share Finance documents',
            'view QA documents', 'create QA documents', 'edit QA documents', 'delete QA documents', 'download QA documents', 'share QA documents',
            'view HR documents', 'create HR documents', 'edit HR documents', 'delete HR documents', 'download HR documents', 'share HR documents',
            'view Purchasing documents', 'create Purchasing documents', 'edit Purchasing documents', 'delete Purchasing documents', 'download Purchasing documents', 'share Purchasing documents',
            'view Sales documents', 'create Sales documents', 'edit Sales documents', 'delete Sales documents', 'download Sales documents', 'share Sales documents',
            'view Operations documents', 'create Operations documents', 'edit Operations documents', 'delete Operations documents', 'download Operations documents', 'share Operations documents',
            'view General documents', 'create General documents', 'edit General documents', 'delete General documents', 'download General documents', 'share General documents',
            // Folders and system
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'manage users', 'manage roles', 'view reports', 'access settings',
        ]);

        // Create VP Sales and Operations role
        $vpSalesOpsRole = Role::create(['name' => 'VP Sales and Operations']);
        $vpSalesOpsRole->givePermissionTo([
            'view Sales documents', 'create Sales documents', 'edit Sales documents', 'delete Sales documents', 'download Sales documents', 'share Sales documents',
            'view Operations documents', 'create Operations documents', 'edit Operations documents', 'delete Operations documents', 'download Operations documents', 'share Operations documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
        ]);

        // Create Comptroller role
        $comptrollerRole = Role::create(['name' => 'Comptroller']);
        $comptrollerRole->givePermissionTo([
            'view Finance documents', 'create Finance documents', 'edit Finance documents', 'delete Finance documents', 'download Finance documents', 'share Finance documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
        ]);

        // Create IT Head role
        $itHeadRole = Role::create(['name' => 'IT Head']);
        $itHeadRole->givePermissionTo([
            'view IT documents', 'create IT documents', 'edit IT documents', 'delete IT documents', 'download IT documents', 'share IT documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports', 'access settings',
        ]);

        // Create QA Head role
        $qaHeadRole = Role::create(['name' => 'QA Head']);
        $qaHeadRole->givePermissionTo([
            'view QA documents', 'create QA documents', 'edit QA documents', 'delete QA documents', 'download QA documents', 'share QA documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
        ]);

        // Create HR Head role
        $hrHeadRole = Role::create(['name' => 'HR Head']);
        $hrHeadRole->givePermissionTo([
            'view HR documents', 'create HR documents', 'edit HR documents', 'delete HR documents', 'download HR documents', 'share HR documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
        ]);

        // Create Purchasing Head role
        $purchasingHeadRole = Role::create(['name' => 'Purchasing Head']);
        $purchasingHeadRole->givePermissionTo([
            'view Purchasing documents', 'create Purchasing documents', 'edit Purchasing documents', 'delete Purchasing documents', 'download Purchasing documents', 'share Purchasing documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
        ]);

        // Create Business Unit Head 1 role
        $buHead1Role = Role::create(['name' => 'Business Unit Head 1']);
        $buHead1Role->givePermissionTo([
            'view Sales documents', 'create Sales documents', 'edit Sales documents', 'download Sales documents', 'share Sales documents',
            'view Operations documents', 'create Operations documents', 'edit Operations documents', 'download Operations documents', 'share Operations documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'share folders',
            'view reports',
        ]);

        // Create Business Unit Head 2 role
        $buHead2Role = Role::create(['name' => 'Business Unit Head 2']);
        $buHead2Role->givePermissionTo([
            'view Sales documents', 'create Sales documents', 'edit Sales documents', 'download Sales documents', 'share Sales documents',
            'view Operations documents', 'create Operations documents', 'edit Operations documents', 'download Operations documents', 'share Operations documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'share folders',
            'view reports',
        ]);

        // Create read-only roles
        $itReadOnlyRole = Role::create(['name' => 'IT Read Only']);
        $itReadOnlyRole->givePermissionTo([
            'view IT documents',
            'download IT documents',
            'view General documents',
            'download General documents',
            'view folders',
        ]);

        $qaReadOnlyRole = Role::create(['name' => 'QA Read Only']);
        $qaReadOnlyRole->givePermissionTo([
            'view QA documents',
            'download QA documents',
            'view General documents',
            'download General documents',
            'view folders',
        ]);

        $dccReadOnlyRole = Role::create(['name' => 'DCC Read Only']);
        $dccReadOnlyRole->givePermissionTo([
            // Read access to most departments (except sensitive ones like HR)
            'view IT documents',
            'download IT documents',
            'view Finance documents',
            'download Finance documents',
            'view QA documents',
            'download QA documents',
            'view Purchasing documents',
            'download Purchasing documents',
            'view Sales documents',
            'download Sales documents',
            'view Operations documents',
            'download Operations documents',
            'view General documents',
            'download General documents',
            'view folders',
            'view reports',
        ]);

        $businessUnit1ReadOnlyRole = Role::create(['name' => 'Business Unit 1 Read Only']);
        $businessUnit1ReadOnlyRole->givePermissionTo([
            'view Sales documents',
            'download Sales documents',
            'view Operations documents',
            'download Operations documents',
            'view General documents',
            'download General documents',
            'view folders',
        ]);

        $businessUnit2ReadOnlyRole = Role::create(['name' => 'Business Unit 2 Read Only']);
        $businessUnit2ReadOnlyRole->givePermissionTo([
            'view Sales documents',
            'download Sales documents',
            'view Operations documents',
            'download Operations documents',
            'view General documents',
            'download General documents',
            'view folders',
        ]);

        // Create Intern role
        $internRole = Role::create(['name' => 'Intern']);
        $internRole->givePermissionTo([
            'view General documents',
            'download General documents',
            'view folders',
        ]);

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

        $users = [
            ['email' => 'superadmin@smartprobegroup.com', 'name' => 'Super Admin', 'role' => 'SuperAdmin'],
            ['email' => 'dccadmin@smartprobegroup.com', 'name' => 'DCC Admin', 'role' => 'DCCAdmin'],
            ['email' => 'vpsales@smartprobegroup.com', 'name' => 'VP Sales Operations', 'role' => 'VP Sales and Operations'],
            ['email' => 'comptroller@smartprobegroup.com', 'name' => 'Comptroller', 'role' => 'Comptroller'],
            ['email' => 'ithead@smartprobegroup.com', 'name' => 'IT Head', 'role' => 'IT Head'],
            ['email' => 'qahead@smartprobegroup.com', 'name' => 'QA Head', 'role' => 'QA Head'],
            ['email' => 'hrhead@smartprobegroup.com', 'name' => 'HR Head', 'role' => 'HR Head'],
            ['email' => 'purchasinghead@smartprobegroup.com', 'name' => 'Purchasing Head', 'role' => 'Purchasing Head'],
            ['email' => 'buhead1@smartprobegroup.com', 'name' => 'Business Unit Head 1', 'role' => 'Business Unit Head 1'],
            ['email' => 'buhead2@smartprobegroup.com', 'name' => 'Business Unit Head 2', 'role' => 'Business Unit Head 2'],
            ['email' => 'itreader@smartprobegroup.com', 'name' => 'IT Reader', 'role' => 'IT Read Only'],
            ['email' => 'qareader@smartprobegroup.com', 'name' => 'QA Reader', 'role' => 'QA Read Only'],
            ['email' => 'dccreader@smartprobegroup.com', 'name' => 'DCC Reader', 'role' => 'DCC Read Only'],
            ['email' => 'bu1reader@smartprobegroup.com', 'name' => 'Business Unit 1 Reader', 'role' => 'Business Unit 1 Read Only'],
            ['email' => 'bu2reader@smartprobegroup.com', 'name' => 'Business Unit 2 Reader', 'role' => 'Business Unit 2 Read Only'],
            ['email' => 'intern@smartprobegroup.com', 'name' => 'Company Intern', 'role' => 'Intern'],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => bcrypt('password123'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole($userData['role']);
        }
    }
}
