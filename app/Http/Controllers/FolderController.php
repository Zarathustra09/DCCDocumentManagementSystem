<?php

namespace App\Http\Controllers;

use App\Models\BaseFolder;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view folders')->only(['index', 'show']);
        $this->middleware('permission:create folders')->only(['create', 'store']);
        $this->middleware('permission:edit folders')->only(['edit', 'update']);
        $this->middleware('permission:delete folders')->only('destroy');
    }

   public function index(Request $request)
   {
       $selectedBaseFolder = null;
       $baseFolders = BaseFolder::all();

       $foldersQuery = Folder::whereNull('parent_id')
           ->with('baseFolder');

       if ($request->has('base_folder') && $request->base_folder) {
           $selectedBaseFolder = BaseFolder::findOrFail($request->base_folder);
           $foldersQuery->where('base_folder_id', $request->base_folder);
       }

       $folders = $foldersQuery->get();

       return view('folder.index', compact('folders', 'baseFolders', 'selectedBaseFolder'));
   }

    public function create(Request $request)
    {
        $currentFolderId = $request->get('parent_id');
        $baseFolders = BaseFolder::all()->filter(function ($baseFolder) {
            return Auth::user()->can("create {$baseFolder->name} documents");
        });

        // Get folders for potential parent selection
        $folders = collect();
        if ($request->has('base_folder_id')) {
            $folders = Folder::where('base_folder_id', $request->base_folder_id)
                ->accessibleByUser(Auth::user())
                ->get();
        }

        return view('folder.create', compact('folders', 'currentFolderId', 'baseFolders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'base_folder_id' => 'required|exists:base_folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        // Check permissions
        $baseFolder = BaseFolder::find($request->base_folder_id);
        if (!Auth::user()->can("create {$baseFolder->name} documents")) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to create folders in this department.'
                ], 403);
            }
            abort(403, 'You do not have permission to create folders in this department.');
        }

        // Validate parent folder belongs to same base folder
        if ($request->parent_id) {
            $parentFolder = Folder::find($request->parent_id);
            if ($parentFolder->base_folder_id !== $request->base_folder_id) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Parent folder must be in the same department.'
                    ], 400);
                }
                return back()->withErrors(['parent_id' => 'Parent folder must be in the same department.']);
            }
        }

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'base_folder_id' => $request->base_folder_id,
            'description' => $request->description,
        ]);

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully',
                'folder' => $folder
            ]);
        }

        // Regular form submission redirect
        if ($request->parent_id) {
            return redirect()->route('folders.show', $request->parent_id)
                ->with('success', 'Folder created successfully');
        }

        return redirect()->route('folders.index')
            ->with('success', 'Folder created successfully');
    }

    public function show(Folder $folder)
    {
        if (!Auth::user()->can("view {$folder->baseFolder->name} documents") && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have permission to view this folder.');
        }

        $documents = $folder->documents;
        $subfolders = $folder->children;

        return view('folder.show', compact('folder', 'documents', 'subfolders'));
    }

    public function edit(Folder $folder)
    {
        if (!Auth::user()->can("edit {$folder->baseFolder->name} documents") && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have permission to edit this folder.');
        }

        $folders = Folder::accessibleByUser(Auth::user())
            ->where('id', '!=', $folder->id)
            ->where('base_folder_id', $folder->base_folder_id)
            ->get();

        $baseFolders = BaseFolder::all()->filter(function ($baseFolder) {
            return Auth::user()->can("edit {$baseFolder->name} documents");
        });

        return view('folder.edit', compact('folder', 'folders', 'baseFolders'));
    }

    public function update(Request $request, Folder $folder)
    {
        if (!Auth::user()->can("edit {$folder->baseFolder->name} documents") && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'base_folder_id' => 'required|exists:base_folders,id',
            'parent_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $newBaseFolder = BaseFolder::find($request->base_folder_id);
        if (!Auth::user()->can("edit {$newBaseFolder->name} documents")) {
            abort(403, 'You do not have permission to move folders to this base folder.');
        }

        // If parent folder is specified, ensure it's in the same base folder
        if ($request->parent_id) {
            $parentFolder = Folder::find($request->parent_id);
            if ($parentFolder->base_folder_id != $request->base_folder_id) {
                return back()->withErrors(['parent_id' => 'Parent folder must be in the same base folder.']);
            }

            // Prevent circular references
            $parent = $parentFolder;
            while ($parent) {
                if ($parent->id == $folder->id) {
                    return back()->withErrors(['parent_id' => 'Cannot set a subfolder as parent']);
                }
                $parent = $parent->parent;
            }
        }

        $folder->update([
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'base_folder_id' => $request->base_folder_id,
            'description' => $request->description,
        ]);

        if ($folder->parent_id) {
            return redirect()->route('folders.show', $folder->parent)->with('success', 'Folder updated successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Folder updated successfully');
    }

    public function destroy(Folder $folder)
    {
        if (!Auth::user()->can("delete {$folder->baseFolder->name} documents") && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $folder->delete();

        if ($folder->parent_id) {
            return redirect()->route('folders.show', $folder->parent)->with('success', 'Folder deleted successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Folder deleted successfully');
    }

    public function move(Request $request, Folder $folder)
    {
        $request->validate([
            'parent_id' => 'nullable|exists:folders,id'
        ]);

        if (!Auth::user()->can("edit {$folder->baseFolder->name} documents")) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to move this folder.'
            ], 403);
        }

        // Prevent moving folder into itself or its descendants
        if ($request->parent_id) {
            $targetFolder = Folder::findOrFail($request->parent_id);

            // Check if target folder is in the same base folder
            if ($targetFolder->base_folder_id !== $folder->base_folder_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot move folder to a different department.'
                ], 400);
            }

            // Check if trying to move into itself or a descendant
            if ($this->isDescendantOf($targetFolder, $folder)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot move folder into itself or its descendants.'
                ], 400);
            }
        }

        $folder->update(['parent_id' => $request->parent_id]);

        return response()->json([
            'success' => true,
            'message' => 'Folder moved successfully.'
        ]);
    }

    private function isDescendantOf($folder, $ancestor)
    {
        $current = $folder;
        while ($current) {
            if ($current->id === $ancestor->id) {
                return true;
            }
            $current = $current->parent;
        }
        return false;
    }


    public function quickUpdate(Request $request, Folder $folder)
    {
        if (!Auth::user()->can("edit {$folder->baseFolder->name} documents")) {
            return response()->json(['success' => false, 'message' => 'Permission denied'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $folder->name = $request->name;
        $folder->description = $request->description;
        $folder->save();

        return response()->json(['success' => true]);
    }

    public function moveToCategory(Request $request, Folder $folder)
    {
        $request->validate([
            'base_folder_id' => 'nullable|exists:base_folders,id'
        ]);

        $folder->update([
            'base_folder_id' => $request->base_folder_id
        ]);

        $categoryName = $request->base_folder_id
            ? BaseFolder::find($request->base_folder_id)->name
            : 'Uncategorized';

        return response()->json([
            'success' => true,
            'message' => "Folder '{$folder->name}' moved to '{$categoryName}'"
        ]);
    }


}
