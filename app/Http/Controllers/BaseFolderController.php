<?php


namespace App\Http\Controllers;

use App\Models\BaseFolder;
use App\Models\Document;
use App\Models\DocumentRegistrationEntry;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

//        dd(Auth::user()->getAllPermissions());

        return redirect()->route('folders.index')->with('success', 'Base folder created successfully.');
    }
}
