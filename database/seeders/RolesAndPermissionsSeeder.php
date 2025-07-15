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

            // Business Unit 1 documents
            'view Business Unit 1 documents',
            'create Business Unit 1 documents',
            'edit Business Unit 1 documents',
            'delete Business Unit 1 documents',
            'download Business Unit 1 documents',
            'share Business Unit 1 documents',

            // Business Unit 2 documents
            'view Business Unit 2 documents',
            'create Business Unit 2 documents',
            'edit Business Unit 2 documents',
            'delete Business Unit 2 documents',
            'download Business Unit 2 documents',
            'share Business Unit 2 documents',

            // Business Unit 3 documents
            'view Business Unit 3 documents',
            'create Business Unit 3 documents',
            'edit Business Unit 3 documents',
            'delete Business Unit 3 documents',
            'download Business Unit 3 documents',
            'share Business Unit 3 documents',
        ];

        // Create document registration approval permissions
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
            'view reports',
            'view dashboard',
            'manage users',
            'manage roles',
            'view audit logs',
            'backup system',
            'restore system',
        ];

        // Create all permissions
        $allPermissions = array_merge($departmentPermissions, $documentRegistrationPermissions, $folderPermissions, $systemPermissions);
        foreach ($allPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create SuperAdmin role with all permissions
        $superAdminRole = Role::create(['name' => 'SuperAdmin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Create DCCAdmin role with administrative permissions
        $dccAdminRole = Role::create(['name' => 'DCCAdmin']);
        $dccAdminRole->givePermissionTo([
            'view IT documents', 'create IT documents', 'edit IT documents', 'delete IT documents', 'download IT documents', 'share IT documents',
            'view Finance documents', 'create Finance documents', 'edit Finance documents', 'delete Finance documents', 'download Finance documents', 'share Finance documents',
            'view QA documents', 'create QA documents', 'edit QA documents', 'delete QA documents', 'download QA documents', 'share QA documents',
            'view HR documents', 'create HR documents', 'edit HR documents', 'download HR documents', 'share HR documents',
            'view Purchasing documents', 'create Purchasing documents', 'edit Purchasing documents', 'delete Purchasing documents', 'download Purchasing documents', 'share Purchasing documents',
            'view Sales documents', 'create Sales documents', 'edit Sales documents', 'download Sales documents', 'share Sales documents',
            'view Operations documents', 'create Operations documents', 'edit Operations documents', 'download Operations documents', 'share Operations documents',
            'view General documents', 'create General documents', 'edit General documents', 'delete General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports', 'view dashboard', 'manage users', 'manage roles',
            'approve document registration', 'reject document registration', 'require revision for document', 'view all document registrations', 'edit document registration details', 'bulk approve document registrations', 'bulk reject document registrations', 'reassign document approver', 'override approval process',
        ]);

        // Create Document Approver role (dedicated for approval workflow)
        $documentApproverRole = Role::create(['name' => 'Document Approver']);
        $documentApproverRole->givePermissionTo([
            'view IT documents', 'view Finance documents', 'view QA documents', 'view HR documents',
            'view Purchasing documents', 'view Sales documents', 'view Operations documents', 'view General documents',
            'view Business Unit 1 documents', 'view Business Unit 2 documents', 'view Business Unit 3 documents',
            'download IT documents', 'download Finance documents', 'download QA documents', 'download HR documents',
            'download Purchasing documents', 'download Sales documents', 'download Operations documents', 'download General documents',
            'download Business Unit 1 documents', 'download Business Unit 2 documents', 'download Business Unit 3 documents',
            'view folders',
            'approve document registration', 'reject document registration', 'require revision for document',
            'view pending document registrations', 'view all document registrations', 'edit document registration details',
        ]);

        // Create VP Sales and Operations role
        $vpSalesOpsRole = Role::create(['name' => 'VP Sales and Operations']);
        $vpSalesOpsRole->givePermissionTo([
            'view Sales documents', 'create Sales documents', 'edit Sales documents', 'delete Sales documents', 'download Sales documents', 'share Sales documents',
            'view Operations documents', 'create Operations documents', 'edit Operations documents', 'delete Operations documents', 'download Operations documents', 'share Operations documents',
            'view Business Unit 1 documents', 'create Business Unit 1 documents', 'edit Business Unit 1 documents', 'delete Business Unit 1 documents', 'download Business Unit 1 documents', 'share Business Unit 1 documents',
            'view Business Unit 2 documents', 'create Business Unit 2 documents', 'edit Business Unit 2 documents', 'delete Business Unit 2 documents', 'download Business Unit 2 documents', 'share Business Unit 2 documents',
            'view Business Unit 3 documents', 'create Business Unit 3 documents', 'edit Business Unit 3 documents', 'delete Business Unit 3 documents', 'download Business Unit 3 documents', 'share Business Unit 3 documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports', 'view dashboard',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
            'approve document registration', 'reject document registration', 'require revision for document', 'view all document registrations',
        ]);

        // Create Comptroller role
        $comptrollerRole = Role::create(['name' => 'Comptroller']);
        $comptrollerRole->givePermissionTo([
            'view Finance documents', 'create Finance documents', 'edit Finance documents', 'delete Finance documents', 'download Finance documents', 'share Finance documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
        ]);

        // Create IT Head role
        $itHeadRole = Role::create(['name' => 'IT Head']);
        $itHeadRole->givePermissionTo([
            'view IT documents', 'create IT documents', 'edit IT documents', 'delete IT documents', 'download IT documents', 'share IT documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
            'approve document registration', 'reject document registration', 'require revision for document',
        ]);

        // Create QA Head role
        $qaHeadRole = Role::create(['name' => 'QA Head']);
        $qaHeadRole->givePermissionTo([
            'view QA documents', 'create QA documents', 'edit QA documents', 'delete QA documents', 'download QA documents', 'share QA documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
            'approve document registration', 'reject document registration', 'require revision for document',
        ]);

        // Create HR Head role
        $hrHeadRole = Role::create(['name' => 'HR Head']);
        $hrHeadRole->givePermissionTo([
            'view HR documents', 'create HR documents', 'edit HR documents', 'delete HR documents', 'download HR documents', 'share HR documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
        ]);

        // Create Purchasing Head role
        $purchasingHeadRole = Role::create(['name' => 'Purchasing Head']);
        $purchasingHeadRole->givePermissionTo([
            'view Purchasing documents', 'create Purchasing documents', 'edit Purchasing documents', 'delete Purchasing documents', 'download Purchasing documents', 'share Purchasing documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
        ]);

        // Create Business Unit Head roles with approval permissions
        $buHead1Role = Role::create(['name' => 'Business Unit Head 1']);
        $buHead1Role->givePermissionTo([
            'view Business Unit 1 documents', 'create Business Unit 1 documents', 'edit Business Unit 1 documents', 'delete Business Unit 1 documents', 'download Business Unit 1 documents', 'share Business Unit 1 documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
            'approve document registration', 'reject document registration', 'require revision for document',
        ]);

        $buHead2Role = Role::create(['name' => 'Business Unit Head 2']);
        $buHead2Role->givePermissionTo([
            'view Business Unit 2 documents', 'create Business Unit 2 documents', 'edit Business Unit 2 documents', 'delete Business Unit 2 documents', 'download Business Unit 2 documents', 'share Business Unit 2 documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
            'approve document registration', 'reject document registration', 'require revision for document',
        ]);

        $buHead3Role = Role::create(['name' => 'Business Unit Head 3']);
        $buHead3Role->givePermissionTo([
            'view Business Unit 3 documents', 'create Business Unit 3 documents', 'edit Business Unit 3 documents', 'delete Business Unit 3 documents', 'download Business Unit 3 documents', 'share Business Unit 3 documents',
            'view General documents', 'create General documents', 'edit General documents', 'download General documents', 'share General documents',
            'view folders', 'create folders', 'edit folders', 'delete folders', 'share folders',
            'view reports',
            'submit document for approval', 'withdraw document submission', 'view pending document registrations',
            'approve document registration', 'reject document registration', 'require revision for document',
        ]);

        // Update read-only roles with basic submission permission
        $itReadOnlyRole = Role::create(['name' => 'IT Read Only']);
        $itReadOnlyRole->givePermissionTo([
            'view IT documents',
            'download IT documents',
            'view General documents',
            'download General documents',
            'view folders',
            'submit document for approval',
        ]);

        $qaReadOnlyRole = Role::create(['name' => 'QA Read Only']);
        $qaReadOnlyRole->givePermissionTo([
            'view QA documents',
            'download QA documents',
            'view General documents',
            'download General documents',
            'view folders',
            'submit document for approval',
        ]);

        $dccReadOnlyRole = Role::create(['name' => 'DCC Read Only']);
        $dccReadOnlyRole->givePermissionTo([
            'view IT documents', 'download IT documents', 'view Finance documents', 'download Finance documents',
            'view QA documents', 'download QA documents', 'view Purchasing documents', 'download Purchasing documents',
            'view Sales documents', 'download Sales documents', 'view Operations documents', 'download Operations documents',
            'view General documents', 'download General documents',
            'view Business Unit 1 documents', 'download Business Unit 1 documents',
            'view Business Unit 2 documents', 'download Business Unit 2 documents',
            'view Business Unit 3 documents', 'download Business Unit 3 documents',
            'view folders', 'view reports',
            'submit document for approval', 'view pending document registrations',
        ]);

        $businessUnit1ReadOnlyRole = Role::create(['name' => 'Business Unit 1 Read Only']);
        $businessUnit1ReadOnlyRole->givePermissionTo([
            'view Business Unit 1 documents', 'download Business Unit 1 documents',
            'view General documents', 'download General documents',
            'view folders',
            'submit document for approval',
        ]);

        $businessUnit2ReadOnlyRole = Role::create(['name' => 'Business Unit 2 Read Only']);
        $businessUnit2ReadOnlyRole->givePermissionTo([
            'view Business Unit 2 documents', 'download Business Unit 2 documents',
            'view General documents', 'download General documents',
            'view folders',
            'submit document for approval',
        ]);

        $businessUnit3ReadOnlyRole = Role::create(['name' => 'Business Unit 3 Read Only']);
        $businessUnit3ReadOnlyRole->givePermissionTo([
            'view Business Unit 3 documents', 'download Business Unit 3 documents',
            'view General documents', 'download General documents',
            'view folders',
            'submit document for approval',
        ]);

        $generalPublicRole = Role::create(['name' => 'General/Public']);
        $generalPublicRole->givePermissionTo([
            'view General documents',
            'view folders',
        ]);

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
            [
                'name' => 'Document Approver',
                'email' => 'approver@smartprobegroup.com',
                'password' => bcrypt('password'),
                'role' => 'Document Approver'
            ],
            [
                'name' => 'VP Sales Operations',
                'email' => 'vpsales@smartprobegroup.com',
                'password' => bcrypt('password'),
                'role' => 'VP Sales and Operations'
            ],
            [
                'name' => 'Business Unit Head 1',
                'email' => 'buhead1@smartprobegroup.com',
                'password' => bcrypt('password'),
                'role' => 'Business Unit Head 1'
            ],
            [
                'name' => 'Business Unit Head 2',
                'email' => 'buhead2@smartprobegroup.com',
                'password' => bcrypt('password'),
                'role' => 'Business Unit Head 2'
            ],
            [
                'name' => 'Business Unit Head 3',
                'email' => 'buhead3@smartprobegroup.com',
                'password' => bcrypt('password'),
                'role' => 'Business Unit Head 3'
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
