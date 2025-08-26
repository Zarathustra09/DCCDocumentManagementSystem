<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // BaseFolder permissions
            'create basefolders',
            'edit basefolders',
            'delete basefolders',
            'view basefolders',
            // Document registration approval permissions
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
            // Folder permissions
            'view folders',
            'create folders',
            'edit folders',
            'delete folders',
            'share folders',
            // System permissions
            'view reports',
            'view dashboard',
            'manage users',
            'manage roles',
            'view audit logs',
            'backup system',
            'restore system',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
