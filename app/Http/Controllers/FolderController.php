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
            ->latest()
            ->get();

        return view('folder.index', compact('folders'));
    }

   public function create(Request $request)
   {
       $folders = Folder::where('user_id', Auth::id())->get();
       $currentFolderId = $request->get('parent_id'); // Get parent_id from query parameter

       return view('folder.create', compact('folders', 'currentFolderId'));
   }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $folder = Folder::create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('folders.show', $folder)->with('success', 'Folder created successfully');
    }

    public function show(Folder $folder)
    {
        // Check if the user owns this folder or has admin role
//        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
//            abort(403);
//        }

        $documents = $folder->documents;
        $subfolders = $folder->children;

        return view('folder.show', compact('folder', 'documents', 'subfolders'));
    }

    public function edit(Folder $folder)
    {
        // Check if the user owns this folder or has admin role
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $folders = Folder::where('user_id', Auth::id())
            ->where('id', '!=', $folder->id)
            ->get();

        return view('folder.edit', compact('folder', 'folders'));
    }

    public function update(Request $request, Folder $folder)
    {
        // Check if the user owns this folder or has admin role
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        // Prevent circular references
        if ($request->parent_id) {
            $parent = Folder::find($request->parent_id);
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
            'description' => $request->description,
        ]);

        if ($folder->parent_id) {
            return redirect()->route('folders.show', $folder->parent)->with('success', 'Folder updated successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Folder updated successfully');
    }

    public function destroy(Folder $folder)
    {
        // Check if the user owns this folder or has admin role
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        // This will cascade delete subfolders and documents due to foreign key constraints
        $folder->delete();

        if ($folder->parent_id) {
            return redirect()->route('folders.show', $folder->parent)->with('success', 'Folder updated successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Folder updated successfully');
    }

    public function move(Request $request, Folder $folder)
    {
        // Check if the user owns this folder or has admin role
        if ($folder->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        // Prevent moving folder into itself or its descendants
        if ($request->parent_id) {
            $parent = Folder::find($request->parent_id);

            // Check if target folder belongs to the same user
            if ($parent->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
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
