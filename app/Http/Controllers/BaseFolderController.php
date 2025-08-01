<?php


namespace App\Http\Controllers;

use App\Models\BaseFolder;
use App\Models\Document;
use App\Models\DocumentRegistrationEntry;
use App\Models\Folder;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BaseFolderController extends Controller
{
    public function create()
    {
        return view('base_folder.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_folders,name',
            'description' => 'nullable|string|max:500',
        ]);

        $baseFolder = new BaseFolder();
        $baseFolder->name = $request->name;
        $baseFolder->description = $request->description;
        $baseFolder->save();

        // Define new permissions for the base folder
        $permissions = [
            "view {$baseFolder->name} documents",
            "create {$baseFolder->name} documents",
            "edit {$baseFolder->name} documents",
            "delete {$baseFolder->name} documents",
            "download {$baseFolder->name} documents",
            "share {$baseFolder->name} documents",
        ];

        // Create and assign permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Grant permissions to SuperAdmin and DCCAdmin roles
        $roles = Role::whereIn('name', ['SuperAdmin', 'DCCAdmin'])->get();
        foreach ($roles as $role) {
            $role->givePermissionTo($permissions);
        }

        // Return JSON response for AJAX requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Base folder created successfully.',
                'base_folder' => $baseFolder
            ]);
        }

        return response()->json([
                    'success' => true,
                    'message' => 'Base folder created successfully.',
                    'base_folder' => $baseFolder
                ]);
    }

    public function edit(BaseFolder $baseFolder)
    {
        return view('base_folder.edit', compact('baseFolder'));
    }

    public function update(Request $request, BaseFolder $baseFolder)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:base_folders,name,' . $baseFolder->id,
            'description' => 'nullable|string|max:500',
        ]);

        $oldName = $baseFolder->name;
        $baseFolder->update($request->only(['name', 'description']));

        // Update permissions if name changed
        if ($oldName !== $baseFolder->name) {
            $oldPermissions = [
                "view {$oldName} documents",
                "create {$oldName} documents",
                "edit {$oldName} documents",
                "delete {$oldName} documents",
                "download {$oldName} documents",
                "share {$oldName} documents",
            ];

            $newPermissions = [
                "view {$baseFolder->name} documents",
                "create {$baseFolder->name} documents",
                "edit {$baseFolder->name} documents",
                "delete {$baseFolder->name} documents",
                "download {$baseFolder->name} documents",
                "share {$baseFolder->name} documents",
            ];

            // Create new permissions
            foreach ($newPermissions as $permission) {
                Permission::firstOrCreate(['name' => $permission]);
            }

            // Update roles
            $roles = Role::whereIn('name', ['SuperAdmin', 'DCCAdmin'])->get();
            foreach ($roles as $role) {
                $role->revokePermissionTo($oldPermissions);
                $role->givePermissionTo($newPermissions);
            }

            // Delete old permissions
            Permission::whereIn('name', $oldPermissions)->delete();
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Base folder updated successfully.',
                'base_folder' => $baseFolder
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Base folder updated successfully.',
            'base_folder' => $baseFolder
        ]);

    }

    public function destroy(BaseFolder $baseFolder)
    {
        // Check if base folder has folders
        if ($baseFolder->folders()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete base folder that contains folders.'
            ], 422);
        }

        $folderName = $baseFolder->name;

        // Delete associated permissions
        $permissions = [
            "view {$folderName} documents",
            "create {$folderName} documents",
            "edit {$folderName} documents",
            "delete {$folderName} documents",
            "download {$folderName} documents",
            "share {$folderName} documents",
        ];

        Permission::whereIn('name', $permissions)->delete();

        $baseFolder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Base folder deleted successfully.'
        ]);
    }

}
