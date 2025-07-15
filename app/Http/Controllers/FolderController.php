<?php

namespace App\Http\Controllers;

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
        $folders = Folder::with(['children', 'user'])
            ->whereNull('parent_id')
            ->accessibleByUser(Auth::user())
            ->latest()
            ->get()
            ->groupBy('department');



        return view('folder.index', compact('folders'));
    }

    public function create(Request $request)
    {
        $folders = Folder::accessibleByUser(Auth::user())->get();
        $currentFolderId = $request->get('parent_id');
        $departments = $this->getAccessibleDepartments();

        return view('folder.create', compact('folders', 'currentFolderId', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|in:' . implode(',', array_keys(Folder::DEPARTMENTS)),
            'parent_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if user can create documents for this department
        if (!Auth::user()->can("create {$request->department} documents")) {
            abort(403, 'You do not have permission to create folders for this department.');
        }

        // If parent folder is specified, ensure it's in the same department
        if ($request->parent_id) {
            $parentFolder = Folder::find($request->parent_id);
            if ($parentFolder->department !== $request->department) {
                return back()->withErrors(['parent_id' => 'Parent folder must be in the same department.']);
            }
        }

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'department' => $request->department,
            'description' => $request->description,
        ]);

        return redirect()->route('folders.show', $folder)->with('success', 'Folder created successfully');
    }

    public function show(Folder $folder)
    {
        // Check if user can view documents for this department
        if (!Auth::user()->can("view {$folder->department} documents") && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have permission to view this folder.');
        }

        $documents = $folder->documents;
        $subfolders = $folder->children;

        return view('folder.show', compact('folder', 'documents', 'subfolders'));
    }

    public function edit(Folder $folder)
    {
        // Check if user can edit documents for this department
        if (!Auth::user()->can("edit {$folder->department} documents") && !Auth::user()->hasRole('admin')) {
            abort(403, 'You do not have permission to edit this folder.');
        }

        $folders = Folder::accessibleByUser(Auth::user())
            ->where('id', '!=', $folder->id)
            ->where('department', $folder->department)
            ->get();

        $departments = $this->getAccessibleDepartments();

        return view('folder.edit', compact('folder', 'folders', 'departments'));
    }

    public function update(Request $request, Folder $folder)
    {
        // Check if user can edit documents for this department
        if (!Auth::user()->can("edit {$folder->department} documents") && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|in:' . implode(',', array_keys(Folder::DEPARTMENTS)),
            'parent_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if user can create/edit documents for the new department
        if (!Auth::user()->can("edit {$request->department} documents")) {
            abort(403, 'You do not have permission to move folders to this department.');
        }

        // If parent folder is specified, ensure it's in the same department
        if ($request->parent_id) {
            $parentFolder = Folder::find($request->parent_id);
            if ($parentFolder->department !== $request->department) {
                return back()->withErrors(['parent_id' => 'Parent folder must be in the same department.']);
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
            'department' => $request->department,
            'description' => $request->description,
        ]);

        if ($folder->parent_id) {
            return redirect()->route('folders.show', $folder->parent)->with('success', 'Folder updated successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Folder updated successfully');
    }

    public function destroy(Folder $folder)
    {
        // Check if user can delete documents for this department
        if (!Auth::user()->can("delete {$folder->department} documents") && !Auth::user()->hasRole('admin')) {
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
        // Check if user can edit documents for this department
        if (!Auth::user()->can("edit {$folder->department} documents") && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        if ($request->parent_id) {
            $parent = Folder::find($request->parent_id);

            // Check if target folder is in the same department
            if ($parent->department !== $folder->department) {
                return response()->json(['success' => false, 'message' => 'Cannot move folder to a different department'], 400);
            }

            // Check if user can access target folder
            if (!Auth::user()->can("view {$parent->department} documents") && !Auth::user()->hasRole('admin')) {
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

    private function getAccessibleDepartments()
    {
        $departments = [];

        foreach (Folder::DEPARTMENTS as $dept => $name) {
            if (Auth::user()->can("create {$dept} documents")) {
                $departments[$dept] = $name;
            }
        }

        return $departments;
    }
}
