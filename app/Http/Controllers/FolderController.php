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

    public function index()
    {
        // Get base folders accessible by the user
        $baseFolders = BaseFolder::where(function ($query) {
            $query->whereHas('folders', function ($subQuery) {
                $subQuery->accessibleByUser(Auth::user());
            })->orDoesntHave('folders');
        })->with(['folders' => function ($query) {
            $query->with(['children', 'documents', 'user'])
                  ->whereNull('parent_id')
                  ->accessibleByUser(Auth::user())
                  ->latest();
        }])->get();

        // Get orphaned folders accessible by the user
        $orphanedFolders = Folder::with(['children', 'documents', 'user'])
            ->whereNull('parent_id')
            ->whereNull('base_folder_id')
            ->accessibleByUser(Auth::user())
            ->latest()
            ->get();

        return view('folder.index', compact('baseFolders', 'orphanedFolders'));
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
            'base_folder_id' => 'required|exists:base_folders,id',
            'parent_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $baseFolder = BaseFolder::find($request->base_folder_id);

        if (!Auth::user()->can("create {$baseFolder->name} documents")) {
            abort(403, 'You do not have permission to create folders in this base folder.');
        }

        // If parent folder is specified, ensure it's in the same base folder
        if ($request->parent_id) {
            $parentFolder = Folder::find($request->parent_id);
            if ($parentFolder->base_folder_id != $request->base_folder_id) {
                return back()->withErrors(['parent_id' => 'Parent folder must be in the same base folder.']);
            }
        }

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'base_folder_id' => $request->base_folder_id,
            'description' => $request->description,
        ]);

        return redirect()->route('folders.index')->with('success', 'Folder created successfully');
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
        if (!Auth::user()->can("edit {$folder->baseFolder->name} documents") && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        if ($request->parent_id) {
            $parent = Folder::find($request->parent_id);

            // Check if target folder is in the same base folder
            if ($parent->base_folder_id !== $folder->base_folder_id) {
                return response()->json(['success' => false, 'message' => 'Cannot move folder to a different base folder'], 400);
            }

            // Check if user can access target folder
            if (!Auth::user()->can("view {$parent->baseFolder->name} documents") && !Auth::user()->hasRole('admin')) {
                return response()->json(['success' => false, 'message' => 'Cannot move to this folder'], 403);
            }

            // Check for circular reference
            $current = $parent;
            while ($current) {
                if ($current->id == $folder->id) {
                    return response()->json(['success' => false, 'message' => 'Cannot move folder into itself or its subfolder'], 400);
                }
                $current = $current->parent;
            }
        }

        $folder->update(['parent_id' => $request->parent_id]);

        return response()->json([
            'success' => true,
            'message' => 'Folder moved successfully'
        ]);
    }
}
