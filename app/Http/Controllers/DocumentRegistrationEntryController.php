<?php

namespace App\Http\Controllers;

use App\Models\DocumentRegistrationEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DocumentRegistrationEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRegistrationEntry::with(['submittedBy', 'approvedBy']);

        // Apply filters based on user permissions
        if (Auth::user()->can('view all document registrations')) {
            // User can see all entries
        } else {
            // User can only see their own submissions
            $query->where('submitted_by', Auth::id());
        }

        // Apply status filter - fix the condition here
        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->where('status', $request->status);
        }

        // Apply search
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_no', 'like', "%{$search}%")
                  ->orWhere('document_title', 'like', "%{$search}%")
                  ->orWhere('device_name', 'like', "%{$search}%")
                  ->orWhere('originator_name', 'like', "%{$search}%");
            });
        }

        $entries = $query->latest()->paginate(15);

        return view('document-registry.index', compact('entries'));
    }

    public function create()
    {
        if (!Auth::user()->can('submit document for approval')) {
            abort(403, 'You do not have permission to submit documents for approval.');
        }

        return view('document-registry.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('submit document for approval')) {
            abort(403);
        }

        $request->validate([
            'document_no' => 'required|string|max:100|unique:document_registration_entries',
            'document_title' => 'required|string|max:255',
            'revision_no' => 'required|string|max:50',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'customer' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx,csv|max:10240'
        ]);

        // Handle file upload
        $filePath = null;
        $originalFilename = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalFilename = $file->getClientOriginalName();
            $filePath = $file->store('document_registrations', 'local');
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
        }

        $entry = DocumentRegistrationEntry::create([
            'document_no' => $request->document_no,
            'document_title' => $request->document_title,
            'revision_no' => $request->revision_no,
            'device_name' => $request->device_name,
            'originator_name' => $request->originator_name,
            'customer' => $request->customer,
            'remarks' => $request->remarks,
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'status' => 'pending',
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
        ]);

        return redirect()->route('document-registry.show', $entry)
            ->with('success', 'Document registration submitted successfully and is pending approval.');
    }

    public function show(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // Check permissions
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403, 'You do not have permission to view this document registration.');
        }

        $documentRegistrationEntry->load(['submittedBy', 'approvedBy', 'documents']);

        return view('document-registry.show', compact('documentRegistrationEntry'));
    }

    public function edit(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canEditEntry($documentRegistrationEntry)) {
            abort(403);
        }

        return view('document-registry.edit', compact('documentRegistrationEntry'));
    }

    public function update(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canEditEntry($documentRegistrationEntry)) {
            abort(403);
        }

        $request->validate([
            'document_title' => 'required|string|max:255',
            'revision_no' => 'required|string|max:50',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'customer' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $documentRegistrationEntry->update($request->only([
            'document_title', 'revision_no', 'device_name',
            'originator_name', 'customer', 'remarks'
        ]));

        return redirect()->route('document-registry.show', $documentRegistrationEntry)
            ->with('success', 'Document registration updated successfully.');
    }

    public function approve(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('approve document registration') ||
            $documentRegistrationEntry->status !== 'pending') {
            abort(403);
        }

        $documentRegistrationEntry->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => null,
            'revision_notes' => null,
        ]);

        return back()->with('success', 'Document registration approved successfully.');
    }

    public function reject(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('reject document registration') ||
            $documentRegistrationEntry->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $documentRegistrationEntry->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'revision_notes' => null,
        ]);

        return back()->with('success', 'Document registration rejected.');
    }

    public function requireRevision(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('require revision for document') ||
            $documentRegistrationEntry->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'revision_notes' => 'required|string'
        ]);

        // Since you only have 3 statuses, we'll reject with revision notes
        $documentRegistrationEntry->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'revision_notes' => $request->revision_notes,
            'rejection_reason' => 'Revision required. Please see revision notes.',
        ]);

        return back()->with('success', 'Revision requested for document registration.');
    }

    public function withdraw(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('withdraw document submission') ||
            $documentRegistrationEntry->submitted_by !== Auth::id() ||
            $documentRegistrationEntry->status !== 'pending') {
            abort(403);
        }

        // Delete the entry since there's no draft status
        $documentRegistrationEntry->delete();

        return redirect()->route('document-registry.index')
            ->with('success', 'Document registration withdrawn successfully.');
    }

    public function bulkApprove(Request $request)
    {
        if (!Auth::user()->can('bulk approve document registrations')) {
            abort(403);
        }

        $request->validate([
            'entries' => 'required|array',
            'entries.*' => 'exists:document_registration_entries,id'
        ]);

        $count = DocumentRegistrationEntry::whereIn('id', $request->entries)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now()
            ]);

        return back()->with('success', "{$count} document registrations approved successfully.");
    }

    public function bulkReject(Request $request)
    {
        if (!Auth::user()->can('bulk reject document registrations')) {
            abort(403);
        }

        $request->validate([
            'entries' => 'required|array',
            'entries.*' => 'exists:document_registration_entries,id',
            'rejection_reason' => 'required|string'
        ]);

        $count = DocumentRegistrationEntry::whereIn('id', $request->entries)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason
            ]);

        return back()->with('success', "{$count} document registrations rejected.");
    }

    public function reassignApprover(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('reassign document approver')) {
            abort(403);
        }

        $request->validate([
            'approver_id' => 'required|exists:users,id'
        ]);

        // Verify the new approver has approval permissions
        $newApprover = User::find($request->approver_id);
        if (!$newApprover->can('approve document registration')) {
            return back()->withErrors(['approver_id' => 'Selected user does not have approval permissions.']);
        }

        // Add reassignment logic here based on your workflow requirements
        return back()->with('success', 'Document approver reassigned successfully.');
    }

    public function overrideApproval(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('override approval process')) {
            abort(403);
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'reason' => 'required|string'
        ]);

        if ($request->action === 'approve') {
            $documentRegistrationEntry->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
        } else {
            $documentRegistrationEntry->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->reason,
            ]);
        }

        return back()->with('success', 'Approval process overridden successfully.');
    }

    private function canViewEntry(DocumentRegistrationEntry $entry)
    {
        return Auth::user()->can('view all document registrations') ||
               ($entry->submitted_by === Auth::id() && Auth::user()->can('view own document registrations'));
    }

    private function canEditEntry(DocumentRegistrationEntry $entry)
    {
        return Auth::user()->can('edit document registration details') &&
               $entry->submitted_by === Auth::id() &&
               $entry->status === 'pending';
    }

    public function download(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403, 'You do not have permission to download this file.');
        }

        if (!$documentRegistrationEntry->hasFile()) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download(
            $documentRegistrationEntry->file_path,
            $documentRegistrationEntry->original_filename
        );
    }

    public function preview(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403, 'You do not have permission to view this file.');
        }

        if (!$documentRegistrationEntry->hasFile()) {
            abort(404, 'File not found.');
        }

        if (!Storage::disk('local')->exists($documentRegistrationEntry->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('local')->path($documentRegistrationEntry->file_path);

        // For PDFs and images, return the file with inline disposition
        if (str_contains($documentRegistrationEntry->mime_type, 'pdf') ||
            str_contains($documentRegistrationEntry->mime_type, 'image')) {
            return response()->file($filePath, [
                'Content-Type' => $documentRegistrationEntry->mime_type,
                'Content-Disposition' => 'inline; filename="' . $documentRegistrationEntry->original_filename . '"'
            ]);
        }

        // For other file types, return JSON response for AJAX handling
        return response()->json([
            'success' => false,
            'message' => 'Preview not available for this file type'
        ], 400);
    }

    public function previewApi(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            return response()->json([
                'success' => false,
                'message' => 'Permission denied'
            ], 403);
        }

        if (!$documentRegistrationEntry->hasFile()) {
            return response()->json([
                'success' => false,
                'message' => 'No file attached'
            ], 404);
        }

        try {
            $filePath = Storage::disk('local')->path($documentRegistrationEntry->file_path);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            // Validate file size (limit to 10MB for preview)
            $fileSize = filesize($filePath);
            if ($fileSize > 10 * 1024 * 1024) {
                return response()->json([
                    'success' => false,
                    'message' => 'File too large for preview. Please download to view.'
                ], 400);
            }

            $extension = strtolower(pathinfo($documentRegistrationEntry->original_filename, PATHINFO_EXTENSION));
            $mimeType = $documentRegistrationEntry->mime_type;

            // Handle Word documents
            if (in_array($extension, ['doc', 'docx']) || str_contains($mimeType, 'word')) {
                return $this->previewWordDocument($filePath);
            }

            // Handle spreadsheet files
            if (in_array($extension, ['xls', 'xlsx', 'csv']) || str_contains($mimeType, 'spreadsheet') || str_contains($mimeType, 'excel')) {
                return $this->previewSpreadsheet($filePath);
            }

            // Handle text files
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
            $spreadsheet = IOFactory::load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            $highestRow = $worksheet->getHighestRow();
            $highestColumn = $worksheet->getHighestColumn();

            // Limit columns to prevent memory issues (max 20 columns)
            $maxColumn = min(20, \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn));
            $limitedHighestColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($maxColumn);

            // Get data with row/column limits
            $data = $worksheet->rangeToArray('A1:' . $limitedHighestColumn . min(100, $highestRow), null, true, true);

            if (empty($data)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spreadsheet appears to be empty'
                ], 400);
            }

            // First row as headings
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

            // Limit content size for preview (first 50KB)
            if (strlen($content) > 50000) {
                $content = substr($content, 0, 50000) . "\n\n... (content truncated for preview)";
            }

            // Convert to HTML with proper formatting
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

        // Add thead
        if (!empty($headings)) {
            $html .= '<thead><tr>';
            foreach ($headings as $heading) {
                $html .= '<th>' . htmlspecialchars($heading ?? '') . '</th>';
            }
            $html .= '</tr></thead>';
        }

        // Add tbody
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

        // Add info message if content is truncated
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

    public function search(Request $request)
    {
        $query = $request->get('q');
        $page = $request->get('page', 1);
        $perPage = 10;

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

    public function list(Request $request)
    {
        $query = DocumentRegistrationEntry::with(['submittedBy', 'approvedBy']);

        // Apply permissions filter
        if (Auth::user()->can('view all document registrations')) {
            // User can see all entries
        } else {
            // User can only see their own submissions
            $query->where('submitted_by', Auth::id());
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply search filter (document number, title, originator)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_no', 'like', "%{$search}%")
                    ->orWhere('document_title', 'like', "%{$search}%")
                    ->orWhere('originator_name', 'like', "%{$search}%");
            });
        }

        // Apply customer filter
        if ($request->filled('customer')) {
            $query->where('customer', 'like', "%{$request->customer}%");
        }

        // Apply device name filter
        if ($request->filled('device_name')) {
            $query->where('device_name', 'like', "%{$request->device_name}%");
        }

        // Apply submitted by filter
        if ($request->filled('submitted_by')) {
            $query->where('submitted_by', $request->submitted_by);
        }

        // Apply date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        // Apply file attachment filter
        if ($request->filled('has_file')) {
            if ($request->has_file === 'yes') {
                $query->whereNotNull('file_path');
            } elseif ($request->has_file === 'no') {
                $query->whereNull('file_path');
            }
        }

        // Get unique values for filter dropdowns
        $customers = DocumentRegistrationEntry::whereNotNull('customer')
            ->distinct()
            ->pluck('customer')
            ->sort();

        $deviceNames = DocumentRegistrationEntry::whereNotNull('device_name')
            ->distinct()
            ->pluck('device_name')
            ->sort();

        $submitters = User::whereIn('id', DocumentRegistrationEntry::distinct()->pluck('submitted_by'))
            ->orderBy('name')
            ->get();

        // Get entries with pagination
        $entries = $query->latest('submitted_at')->paginate(15);

        // Statistics for results summary
        $totalEntries = $query->count();
        $pendingCount = (clone $query)->where('status', 'pending')->count();
        $approvedCount = (clone $query)->where('status', 'approved')->count();
        $rejectedCount = (clone $query)->where('status', 'rejected')->count();

        return view('document-registry.list', compact(
            'entries',
            'customers',
            'deviceNames',
            'submitters',
            'totalEntries',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }
}
