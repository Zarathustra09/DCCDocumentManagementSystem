<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view documents')->only(['index', 'show']);
        $this->middleware('permission:create documents')->only(['create', 'store']);
        $this->middleware('permission:edit documents')->only(['edit', 'update']);
        $this->middleware('permission:delete documents')->only('destroy');
        $this->middleware('permission:download documents')->only('download');
    }

    public function index(Request $request)
    {
        $query = Document::where('user_id', Auth::id());

        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }

        $documents = $query->latest()->paginate(10);
        $folders = Folder::where('user_id', Auth::id())->get();

        return view('document.index', compact('documents', 'folders'));
    }

    public function create()
    {
        $folders = Folder::where('user_id', Auth::id())->get();
        return view('document.create', compact('folders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $filename = time() . '_' . Str::slug(pathinfo($originalFilename, PATHINFO_FILENAME)) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents', $filename, 'public');

        Document::create([
            'user_id' => Auth::id(),
            'folder_id' => $request->folder_id,
            'filename' => $filename,
            'original_filename' => $originalFilename,
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $request->description,
            'meta_data' => [
                'uploaded_at' => now()->toDateTimeString(),
                'ip' => $request->ip(),
            ],
        ]);

        return redirect()->route('documents.index')->with('success', 'Document uploaded successfully');
    }

    public function show(Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return view('document.show', compact('document'));
    }

    public function edit(Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $folders = Folder::where('user_id', Auth::id())->get();
        return view('document.edit', compact('document', 'folders'));
    }

    public function update(Request $request, Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $request->validate([
            'folder_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        $document->update([
            'folder_id' => $request->folder_id,
            'description' => $request->description,
        ]);

        return redirect()->route('documents.index')->with('success', 'Document updated successfully');
    }

    public function destroy(Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        // Delete the actual file
        Storage::disk('public')->delete($document->file_path);

        // Delete the database record
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully');
    }

    public function download(Document $document)
    {
        // Check if the user owns this document or has admin role
        if ($document->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }

        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_filename
        );
    }
}
