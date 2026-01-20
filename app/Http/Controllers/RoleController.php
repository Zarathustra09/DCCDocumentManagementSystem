<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\DataTables\RolesDataTable;

class RoleController extends Controller
{
    /**
     * Display a listing of roles with their permissions.
     */
    public function index(RolesDataTable $dataTable)
    {
        // collection for the quick-stats cards
        $roles = Role::with(['permissions', 'users'])->get();
        $totalPermissions = Permission::count();
        $rolesWithoutPermissions = Role::doesntHave('permissions')->count();

        return $dataTable->render('role.index', compact('roles', 'totalPermissions', 'rolesWithoutPermissions'));
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

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $allPermissions = Permission::all()->groupBy(function ($permission) {
            // Group permissions by category (first word before space)
            $parts = explode(' ', $permission->name);
            if (in_array($parts[0], ['view', 'create', 'edit', 'delete', 'download', 'share'])) {
                return implode(' ', array_slice($parts, 1));
            }
            return 'System';
        });

        return view('role.create', compact('allPermissions'));
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        try {
            $role = Role::create(['name' => $request->name]);

            if ($request->permissions) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'redirect' => route('roles.show', $role)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the name of the specified role.
     */
    public function updateName(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id
        ]);

        try {
            $role->update(['name' => $request->name]);

            return response()->json([
                'success' => true,
                'message' => 'Role name updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role name: ' . $e->getMessage()
            ], 500);
        }
    }
}
