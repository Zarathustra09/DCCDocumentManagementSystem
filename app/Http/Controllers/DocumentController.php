<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentRegistrationEntry;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    //TODO: to add download functionality for documents
    //TODO: to add edit functionality for documents
    //TODO: to remove close preview in  document.show
    public function __construct()
    {

    }

    // In app/Http/Controllers/DocumentController.php - update create method
    public function create(Request $request)
    {
        $currentFolderId = $request->get('folder_id');
        $currentFolder = null;
        $preselectedDepartment = null;

        if ($currentFolderId) {
            $currentFolder = Folder::find($currentFolderId);
            if ($currentFolder && $currentFolder->baseFolder && Auth::user()->can("create {$currentFolder->baseFolder->name} documents")) {
                $preselectedDepartment = $currentFolder->baseFolder->name;
            }
        }

        // Get available base folders instead of departments
        $availableBaseFolders = [];
        foreach (\App\Models\BaseFolder::all() as $baseFolder) {
            if (Auth::user()->can("create {$baseFolder->name} documents")) {
                $availableBaseFolders[$baseFolder->name] = $baseFolder->name;
            }
        }

        if (empty($availableBaseFolders)) {
            abort(403, 'You do not have permission to create documents for any department.');
        }

        $folders = Folder::accessibleByUser(Auth::user())->get();

        $registrationEntries = DocumentRegistrationEntry::where('status', 'approved')
            ->orderBy('document_title')
            ->get();

        return view('document.create', compact(
            'folders',
            'currentFolderId',
            'currentFolder',
            'preselectedDepartment',
            'availableBaseFolders',
            'registrationEntries'
        ));
    }
    public function show(Document $document)
    {
        // Check if user can view documents for this department
        if (!Auth::user()->can("view {$document->baseFolder->name} documents")) {
            abort(403, 'You do not have permission to view this document.');
        }

        return view('document.show', compact('document'));
    }

    public function store(Request $request)
    {

        Log::info('Document upload request received', [
            'user_id' => Auth::id(),
            'base_folder_name' => $request->base_folder_name,
            'folder_id' => $request->folder_id,
            'document_registration_entry_id' => $request->document_registration_entry_id,
            'description' => $request->description
        ]);
        // Get available base folder names for validation


        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif,xls,xlsx|max:10240',
            'folder_id' => 'nullable|exists:folders,id',
            'document_registration_entry_id' => 'nullable|exists:document_registration_entries,id',
            'description' => 'nullable|string|max:500',
        ]);

        $base_folder_name = Folder::find($request->folder_id)->baseFolder->name ?? null;

        if (!Auth::user()->can("create {$base_folder_name} documents")) {
            abort(403, 'You do not have permission to create documents for this department.');
        }

        // Get the base folder ID
        $baseFolder = \App\Models\BaseFolder::where('name', $base_folder_name)->first();

        if ($request->folder_id) {
            $folder = Folder::find($request->folder_id);
            if ($folder->base_folder_id !== $baseFolder->id) {
                return back()->withErrors(['folder_id' => 'Selected folder must be in the same department.']);
            }
        }

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename = time() . '_' . $originalName;
        $filePath = $file->store('documents', 'local');

        $document = Document::create([
            'user_id' => Auth::id(),
            'folder_id' => $request->folder_id,
            'base_folder_id' => $baseFolder->id,
            'document_registration_entry_id' => $request->document_registration_entry_id,
            'filename' => $filename,
            'original_filename' => $originalName,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'description' => $request->description,
        ]);

        if ($request->folder_id) {
            return redirect()->route('folders.show', $request->folder_id)->with('success', 'Document uploaded successfully');
        }

        return redirect()->route('folders.index')->with('success', 'Document uploaded successfully');
    }
    public function edit(Document $document)
    {
        if (!Auth::user()->can("edit {$document->baseFolder->name} documents")) {
            abort(403, 'You do not have permission to edit this document.');
        }

        // Get available base folders
        $baseFolders = [];
        foreach (\App\Models\BaseFolder::all() as $baseFolder) {
            if (Auth::user()->can("edit {$baseFolder->name} documents")) {
                $baseFolders[$baseFolder->name] = $baseFolder->name;
            }
        }

        $folders = Folder::accessibleByUser(Auth::user())
            ->where('base_folder_id', $document->base_folder_id)
            ->get();

        $registrationEntries = DocumentRegistrationEntry::where('status', 'approved')
            ->orderBy('document_title')
            ->get();

        return view('document.edit', compact('document', 'folders', 'baseFolders', 'registrationEntries'));
    }

    public function update(Request $request, Document $document)
    {
        if (!Auth::user()->can("edit {$document->baseFolder->name} documents")) {
            abort(403);
        }

        $baseFolderNames = \App\Models\BaseFolder::pluck('name')->toArray();

        $request->validate([
            'base_folder_name' => 'required|in:' . implode(',', $baseFolderNames),
            'folder_id' => 'nullable|exists:folders,id',
            'document_registration_entry_id' => 'nullable|exists:document_registration_entries,id',
            'description' => 'nullable|string|max:500',
        ]);

        if (!Auth::user()->can("edit {$request->base_folder_name} documents")) {
            abort(403, 'You do not have permission to move documents to this department.');
        }

        // Get the base folder ID
        $baseFolder = \App\Models\BaseFolder::where('name', $request->base_folder_name)->first();

        if ($request->folder_id) {
            $folder = Folder::find($request->folder_id);
            if ($folder->base_folder_id !== $baseFolder->id) {
                return back()->withErrors(['folder_id' => 'Selected folder must be in the same department.']);
            }
        }

        $document->update([
            'folder_id' => $request->folder_id,
            'base_folder_id' => $baseFolder->id,
            'document_registration_entry_id' => $request->document_registration_entry_id,
            'description' => $request->description,
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

    /**
     * Preview a document.
     *
     * @param Document $document
     * @return \Illuminate\Http\JsonResponse
     */
    public function move(Request $request, Document $document)
     {
         $request->validate([
             'folder_id' => 'nullable|exists:folders,id'
         ]);

         if (!Auth::user()->can("edit {$document->baseFolder->name} documents")) {
             return response()->json([
                 'success' => false,
                 'message' => 'You do not have permission to move this document.'
             ], 403);
         }

         if ($request->folder_id) {
             $folder = Folder::findOrFail($request->folder_id);
             if ($folder->base_folder_id !== $document->base_folder_id) {
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



    public function search(Request $request)
    {
        $query = $request->get('q');
        $page = $request->get('page', 1);
        $perPage = 10; // Number of items per page

        $entries = DocumentRegistrationEntry::where('status', 'approved')
            ->where(function($q) use ($query) {
                $q->where('document_no', 'like', "%{$query}%")
                    ->orWhere('document_title', 'like', "%{$query}%")
                    ->orWhere('device_name', 'like', "%{$query}%")
                    ->orWhere('originator_name', 'like', "%{$query}%");
            })
            ->orderBy('document_title')
            ->paginate($perPage, ['*'], 'page', $page);

        $morePages = $entries->hasMorePages();

        $results = [];
        foreach ($entries as $entry) {
            $results[] = [
                'id' => $entry->id,
                'text' => "{$entry->document_no} - {$entry->document_title}" .
                    ($entry->device_name ? " ({$entry->device_name})" : "")
            ];
        }

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => $morePages
            ]
        ]);
    }


    public function preview(Document $document)
    {
        if (!Auth::user()->can("view {$document->baseFolder->name} documents")) {
            abort(403, 'You do not have permission to view this file.');
        }

        if (!Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('local')->path($document->file_path);

        if (str_contains($document->mime_type, 'pdf') || str_contains($document->mime_type, 'image')) {
            return response()->file($filePath, [
                'Content-Type' => $document->mime_type,
                'Content-Disposition' => 'inline; filename="' . $document->original_filename . '"'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Preview not available for this file type'
        ], 400);
    }

    public function previewApi(Document $document)
    {
        if (!Auth::user()->can("view {$document->baseFolder->name} documents")) {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied'
            ], 403);
        }

        if (!$document || !Storage::disk('local')->exists($document->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        try {
            $filePath = Storage::disk('local')->path($document->file_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            $fileSize = filesize($filePath);
            if ($fileSize > 10 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File too large for preview. Please download to view.'
                ], 400);
            }

            $extension = strtolower(pathinfo($document->original_filename, PATHINFO_EXTENSION));
            $mimeType = $document->mime_type;

            if (in_array($extension, ['doc', 'docx']) || str_contains($mimeType, 'word')) {
                return $this->previewWordDocument($filePath);
            }

            if (in_array($extension, ['xls', 'xlsx', 'csv']) || str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel')) {
                return $this->previewSpreadsheet($filePath);
            }

            if (in_array($extension, ['txt', 'csv']) || str_contains($mimeType, 'text')) {
                return $this->previewTextFile($filePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Preview not available for this file type'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while generating the preview'
            ], 500);
        }
    }

    private function previewWordDocument($filePath)
    {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
        } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document appears to be corrupted or uses an unsupported format'
            ], 400);
        }

        try {
            $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');
            $tempFile = tempnam(sys_get_temp_dir(), 'phpword_preview_');
            $htmlWriter->save($tempFile);
            $htmlContent = file_get_contents($tempFile);
            unlink($tempFile);

            if (empty($htmlContent)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document appears to be empty'
                ], 400);
            }

            $htmlContent = $this->cleanWordHtml($htmlContent);

            return response()->json([
                'success' => true,
                'content' => $htmlContent,
                'content_type' => 'word'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error converting document to preview format'
            ], 500);
        }
    }

    private function previewSpreadsheet($filePath)
    {
        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();
            $maxColumn = min(20, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn));
            $limitedHighestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxColumn);

            $data = $worksheet->rangeToArray('A1:' . $limitedHighestColumn . min(100, $highestRow), null, true, true);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spreadsheet appears to be empty'
                ], 400);
            }

            $headings = array_shift($data);
            $html = $this->generateSpreadsheetHtml($headings, $data, $highestRow);

            return response()->json([
                'success' => true,
                'content' => $html,
                'content_type' => 'spreadsheet'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading spreadsheet file'
            ], 500);
        }
    }

    private function previewTextFile($filePath)
    {
        try {
            $content = file_get_contents($filePath);
            if ($content === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to read text file'
                ], 500);
            }

            if (strlen($content) > 50000) {
                $content = substr($content, 0, 50000) . "\n\n... (content truncated for preview)";
            }

            $html = '<div class="text-preview-content">' . htmlspecialchars($content) . '</div>';

            return response()->json([
                'success' => true,
                'content' => $html,
                'content_type' => 'text'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reading text file'
            ], 500);
        }
    }

    private function generateSpreadsheetHtml($headings, $data, $totalRows)
    {
        $html = '<div class="spreadsheet-preview">';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-sm">';

        if (!empty($headings)) {
            $html .= '<thead><tr>';
            foreach ($headings as $heading) {
                $html .= '<th>' . htmlspecialchars($heading ?? '') . '</th>';
            }
            $html .= '</tr></thead>';
        }

        $html .= '<tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . htmlspecialchars($cell ?? '') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</div>';

        if ($totalRows > 100) {
            $html .= '<div class="alert alert-info mt-2 mb-0">';
            $html .= '<small><i class="bx bx-info-circle"></i> Showing first 100 rows of ' . $totalRows . ' total rows. Download the file to view all data.</small>';
            $html .= '</div>';
        }

        $html .= '</div>';
        return $html;
    }

    private function cleanWordHtml($html)
    {
        $html = preg_replace('/<\?xml[^>]*>/', '', $html);
        $html = preg_replace('/<!DOCTYPE[^>]*>/', '', $html);
        $html = preg_replace('/<html[^>]*>/', '<div class="word-document">', $html);
        $html = str_replace('</html>', '</div>', $html);
        $html = preg_replace('/<head[^>]*>.*?<\/head>/s', '', $html);
        $html = preg_replace('/<body[^>]*>/', '', $html);
        $html = str_replace('</body>', '', $html);
        $html = preg_replace('/style="[^"]*?(font-family|color|text-align|font-weight|font-style)[^"]*?"/', '', $html);
        $html = preg_replace('/\s+/', ' ', $html);
        $html = str_replace('> <', '><', $html);
        $html = str_replace('<p></p>', '', $html);
        return trim($html);
    }
}
