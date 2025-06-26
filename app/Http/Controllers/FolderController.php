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

    public function create()
    {
        $folders = Folder::where('user_id', Auth::id())->get();
        return view('folder.create', compact('folders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        Folder::create([
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('folders.index')->with('success', 'Folder created successfully');
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

        return redirect()->route('folders.index')->with('success', 'Folder and all its contents deleted successfully');
    }
}
