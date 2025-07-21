<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();

        // Fix: Use query builder instead of model directly
        $usersWithoutRoles = User::with('roles')->get()->filter(
            fn ($user) => $user->roles->where('name', 'Manager')->toArray()
        )->count();

        return view('user-roles.index', compact('users', 'roles', 'usersWithoutRoles'));
    }

    public function show(User $user)
    {
        $user->load('roles', 'permissions');

        // Get all available roles and permissions
        $roles = Role::all();
        $permissions = Permission::all();

        // Get permissions through roles
        $rolePermissions = $user->getPermissionsViaRoles();

        // Get direct permissions
        $directPermissions = $user->getDirectPermissions();

        // Get all effective permissions
        $allPermissions = $user->getAllPermissions();

        return view('user-roles.show', compact(
            'user',
            'roles',
            'permissions',
            'rolePermissions',
            'directPermissions',
            'allPermissions'
        ));
    }

    public function updateUserRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id'
        ]);

        try {
            // Convert role IDs to role names for Spatie
            $roleNames = Role::whereIn('id', $request->roles ?? [])
                ->pluck('name')
                ->toArray();

            // Sync roles using role names
            $user->syncRoles($roleNames);

            return response()->json([
                'success' => true,
                'message' => 'User roles updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateUserPermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            // Get permission names from IDs
            $permissionNames = Permission::whereIn('id', $request->permissions ?? [])
                ->pluck('name')
                ->toArray();

            // Sync direct permissions using permission names
            $user->syncPermissions($permissionNames);

            return response()->json([
                'success' => true,
                'message' => 'User permissions updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
