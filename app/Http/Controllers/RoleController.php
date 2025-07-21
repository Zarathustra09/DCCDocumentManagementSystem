<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display a listing of roles with their permissions.
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->get();
        $totalPermissions = Permission::count();
        $rolesWithoutPermissions = Role::doesntHave('permissions')->count();

        return view('role.index', compact('roles', 'totalPermissions', 'rolesWithoutPermissions'));
    }

    /**
     * Display the specified role with detailed permissions.
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        $allPermissions = Permission::all()->groupBy(function ($permission) {
            // Group permissions by category (first word before space)
            $parts = explode(' ', $permission->name);
            if (in_array($parts[0], ['view', 'create', 'edit', 'delete', 'download', 'share'])) {
                return implode(' ', array_slice($parts, 1));
            }
            return 'System';
        });

        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('role.show', compact('role', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Update the permissions for the specified role.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
            $role->syncPermissions($permissions);

            return response()->json([
                'success' => true,
                'message' => 'Role permissions updated successfully.',
                'permissionCount' => $permissions->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}
