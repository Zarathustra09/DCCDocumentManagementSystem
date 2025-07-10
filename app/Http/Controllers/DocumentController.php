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

    }

    public function create(Request $request)
    {
        $currentFolderId = $request->get('folder_id');
        $currentFolder = null;
        $preselectedDepartment = null;

        // Get the current folder if specified
        if ($currentFolderId) {
            $currentFolder = Folder::find($currentFolderId);
            if ($currentFolder && Auth::user()->can("create {$currentFolder->department} documents")) {
                $preselectedDepartment = $currentFolder->department;
            }
        }

        // Get available departments (only those user can create documents for)
        $availableDepartments = [];
        foreach (Document::DEPARTMENTS as $dept => $name) {
            if (Auth::user()->can("create {$dept} documents")) {
                $availableDepartments[$dept] = $name;
            }
        }

        // Check if user has permission to create documents for any department
        if (empty($availableDepartments)) {
            abort(403, 'You do not have permission to create documents for any department.');
        }

        // Get folders user can access
        $folders = Folder::accessibleByUser(Auth::user())->get();

        return view('document.create', compact(
            'folders',
            'currentFolderId',
            'currentFolder',
            'preselectedDepartment',
            'availableDepartments'
        ));
    }

    public function show(Document $document)
    {
        // Check if user can view documents for this department
        if (!Auth::user()->can("view {$document->department} documents")) {
            abort(403, 'You do not have permission to view this document.');
        }

        return view('document.show', compact('document'));
    }

    // Rest of your methods remain the same as they already have proper permission checks
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif,xls,xlsx|max:10240',
            'department' => 'required|in:' . implode(',', array_keys(Document::DEPARTMENTS)),
            'folder_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if user can create documents for this department
        if (!Auth::user()->can("create {$request->department} documents")) {
            abort(403, 'You do not have permission to create documents for this department.');
        }

        // If folder is specified, ensure it's in the same department
        if ($request->folder_id) {
            $folder = Folder::find($request->folder_id);
            if ($folder->department !== $request->department) {
                return back()->withErrors(['folder_id' => 'Selected folder must be in the same department.']);
            }
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . $originalName;
        $filePath = $file->store('documents', 'public');

        $document = Document::create([
            'user_id' => Auth::id(),
            'folder_id' => $request->folder_id,
            'filename' => $filename,
            'original_filename' => $originalName,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $request->description,
            'department' => $request->department,
        ]);

        if ($request->folder_id) {
            return redirect()->route('folders.show', $request->folder_id)->with('success', 'Document uploaded successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Document uploaded successfully');
    }

    public function edit(Document $document)
    {
        // Check if user can edit documents for this department
        if (!Auth::user()->can("edit {$document->department} documents")) {
            abort(403, 'You do not have permission to edit this document.');
        }

        // Get available departments (only those user can edit documents for)
        $departments = [];
        foreach (Document::DEPARTMENTS as $dept => $name) {
            if (Auth::user()->can("edit {$dept} documents")) {
                $departments[$dept] = $name;
            }
        }

        // Get folders user can access for the document's department
        $folders = Folder::accessibleByUser(Auth::user())
            ->where('department', $document->department)
            ->get();

        return view('document.edit', compact('document', 'folders', 'departments'));
    }

    public function update(Request $request, Document $document)
    {
        // Check if user can edit documents for this department
        if (!Auth::user()->can("edit {$document->department} documents")) {
            abort(403);
        }

        $request->validate([
            'department' => 'required|in:' . implode(',', array_keys(Document::DEPARTMENTS)),
            'folder_id' => 'nullable|exists:folders,id',
            'description' => 'nullable|string|max:500',
        ]);

        // Check if user can edit documents for the new department
        if (!Auth::user()->can("edit {$request->department} documents")) {
            abort(403, 'You do not have permission to move documents to this department.');
        }

        // If folder is specified, ensure it's in the same department
        if ($request->folder_id) {
            $folder = Folder::find($request->folder_id);
            if ($folder->department !== $request->department) {
                return back()->withErrors(['folder_id' => 'Selected folder must be in the same department.']);
            }
        }

        $document->update([
            'folder_id' => $request->folder_id,
            'description' => $request->description,
            'department' => $request->department,
        ]);

        if ($document->folder_id) {
            return redirect()->route('folders.show', $document->folder_id)->with('success', 'Document updated successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Document updated successfully');
    }
    public function destroy(Document $document)
    {
        // Delete the actual file
        Storage::disk('public')->delete($document->file_path);

        // Store folder_id before deletion
        $folderId = $document->folder_id;

        // Delete the database record
        $document->delete();

        if ($folderId) {
            return redirect()->route('folders.show', $folderId)
                ->with('success', 'Document deleted successfully');
        }

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully');
    }

    public function download(Document $document)
    {
        return Storage::disk('public')->download(
            $document->file_path,
            $document->original_filename
        );
    }

    public function move(Request $request, Document $document)
    {
        $request->validate([
            'folder_id' => 'nullable|exists:folders,id'
        ]);

        // Check if user can edit documents for this department
        if (!Auth::user()->can("edit {$document->department} documents")) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to move this document.'
            ], 403);
        }

        // If moving to a folder, ensure it's in the same department
        if ($request->folder_id) {
            $folder = Folder::findOrFail($request->folder_id);
            if ($folder->department !== $document->department) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot move document to a folder in a different department.'
                ], 400);
            }
        }

        $document->update(['folder_id' => $request->folder_id]);

        return response()->json([
            'success' => true,
            'message' => 'Document moved successfully.'
        ]);
    }

    public function preview(Document $document)
    {
        \Log::info('Document preview requested', [
            'document_id' => $document->id,
            'user_id' => Auth::id(),
            'filename' => $document->original_filename,
            'file_type' => $document->file_type,
            'file_size' => $document->file_size,
            'department' => $document->department
        ]);

        // Check if it's a Word document
        if (!in_array($document->file_type, ['doc', 'docx'])) {
            \Log::info('Preview not available for file type', [
                'document_id' => $document->id,
                'file_type' => $document->file_type,
                'user_id' => Auth::id()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Preview not available for this file type'
            ], 400);
        }

        try {
            $filePath = storage_path('app/public/' . $document->file_path);

            \Log::info('Processing document preview', [
                'document_id' => $document->id,
                'file_path' => $filePath,
                'file_exists' => file_exists($filePath),
                'user_id' => Auth::id()
            ]);

            if (!file_exists($filePath)) {
                \Log::error('Document file not found', [
                    'document_id' => $document->id,
                    'file_path' => $filePath,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Validate file size (limit to 10MB for preview)
            $fileSize = filesize($filePath);
            if ($fileSize > 10 * 1024 * 1024) {
                \Log::warning('Document too large for preview', [
                    'document_id' => $document->id,
                    'file_size' => $fileSize,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'File too large for preview. Please download to view.'
                ], 400);
            }

            // Validate file is actually a Word document by checking mime type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $filePath);
            finfo_close($finfo);

            \Log::info('Document mime type validation', [
                'document_id' => $document->id,
                'detected_mime_type' => $mimeType,
                'stored_mime_type' => $document->mime_type,
                'user_id' => Auth::id()
            ]);

            $validMimeTypes = [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                'application/msword', // .doc
                'application/zip', // Sometimes .docx appears as zip
            ];

            if (!in_array($mimeType, $validMimeTypes)) {
                \Log::warning('Invalid Word document format', [
                    'document_id' => $document->id,
                    'detected_mime_type' => $mimeType,
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Word document format'
                ], 400);
            }

            // Try to load the document with better error handling
            try {
                \Log::info('Loading Word document with PHPWord', [
                    'document_id' => $document->id,
                    'user_id' => Auth::id()
                ]);
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
                \Log::error('PHPWord document loading failed', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Document appears to be corrupted or uses an unsupported format'
                ], 400);
            } catch (\Exception $e) {
                \Log::error('General document loading error', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read document: ' . $e->getMessage()
                ], 500);
            }

            // Convert to HTML with error handling
            try {
                \Log::info('Converting document to HTML', [
                    'document_id' => $document->id,
                    'user_id' => Auth::id()
                ]);

                $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

                // Use temporary file instead of output buffer
                $tempFile = tempnam(sys_get_temp_dir(), 'phpword_preview_');
                $htmlWriter->save($tempFile);

                $htmlContent = file_get_contents($tempFile);
                unlink($tempFile); // Clean up temp file

                \Log::info('Document conversion completed', [
                    'document_id' => $document->id,
                    'html_length' => strlen($htmlContent),
                    'temp_file' => $tempFile,
                    'user_id' => Auth::id()
                ]);

                if (empty($htmlContent)) {
                    \Log::warning('Document conversion resulted in empty content', [
                        'document_id' => $document->id,
                        'user_id' => Auth::id()
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Document appears to be empty or could not be converted'
                    ], 400);
                }

                // Clean up the HTML content
                $htmlContent = $this->cleanWordHtml($htmlContent);

                \Log::info('Document preview generated successfully', [
                    'document_id' => $document->id,
                    'final_html_length' => strlen($htmlContent),
                    'user_id' => Auth::id()
                ]);

                return response()->json([
                    'success' => true,
                    'content' => $htmlContent
                ]);

            } catch (\Exception $e) {
                \Log::error('Document HTML conversion failed', [
                    'document_id' => $document->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'user_id' => Auth::id()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Error converting document to preview format'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('Document preview error', [
                'document_id' => $document->id,
                'file_path' => $filePath ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred while loading the preview'
            ], 500);
        }
    }

    private function cleanWordHtml($html)
    {
        // Remove XML declaration and DOCTYPE
        $html = preg_replace('/<\?xml[^>]*>/', '', $html);
        $html = preg_replace('/<!DOCTYPE[^>]*>/', '', $html);

        // Replace html tags with div container
        $html = preg_replace('/<html[^>]*>/', '<div class="word-document">', $html);
        $html = str_replace('</html>', '</div>', $html);

        // Remove head section entirely
        $html = preg_replace('/<head[^>]*>.*?<\/head>/s', '', $html);

        // Clean body tags
        $html = preg_replace('/<body[^>]*>/', '', $html);
        $html = str_replace('</body>', '', $html);

        // Remove problematic inline styles but keep basic formatting
        $html = preg_replace('/style="[^"]*?(font-family|color|text-align|font-weight|font-style)[^"]*?"/', '', $html);

        // Clean up multiple spaces and line breaks
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace('> <', '><', $html);

        // Ensure proper paragraph spacing
        $html = str_replace('<p></p>', '', $html);

        return trim($html);
    }
}
