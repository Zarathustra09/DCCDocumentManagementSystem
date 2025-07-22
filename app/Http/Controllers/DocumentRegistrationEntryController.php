<?php

namespace App\Http\Controllers;

use App\Models\DocumentRegistrationEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentRegistrationEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRegistrationEntry::with(['submittedBy', 'approvedBy']);

        // Apply filters based on user permissions
        if (Auth::user()->can('view all document registrations')) {
            // Can view all registrations
        } elseif (Auth::user()->can('view pending document registrations')) {
            $query->where(function($q) {
                $q->where('status', 'pending')
                    ->orWhere('submitted_by', Auth::id());
            });
        } else {
            // Can only view own submissions
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
                $q->where('document_title', 'like', "%{$search}%")
                    ->orWhere('document_no', 'like', "%{$search}%")
                    ->orWhere('originator_name', 'like', "%{$search}%")
                    ->orWhere('customer', 'like', "%{$search}%");
            });
        }

        $entries = $query->latest()->paginate(15);

        return view('document-registry.index', compact('entries'));
    }

    public function create()
    {
        if (!Auth::user()->can('submit document for approval')) {
            abort(403);
        }

        return view('document-registry.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('submit document for approval')) {
            abort(403);
        }

        $request->validate([
            'document_title' => 'required|string|max:255',
            'document_no' => 'required|string|max:255|unique:document_registration_entries',
            'revision_no' => 'required|string|max:10',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'customer' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
            'document_file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,txt',
        ]);

        // Handle file upload
        $filePath = null;
        $originalFilename = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $originalFilename = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // Generate unique filename
            $filename = time() . '_' . preg_replace('/[^A-Za-z0-9\-\.]/', '_', $originalFilename);
            $filePath = $file->storeAs('document-registrations', $filename, 'local'); // Changed from 'private' to 'local'
        }

        $entry = DocumentRegistrationEntry::create([
            'document_title' => $request->document_title,
            'document_no' => $request->document_no,
            'revision_no' => $request->revision_no,
            'device_name' => $request->device_name,
            'originator_name' => $request->originator_name,
            'customer' => $request->customer,
            'remarks' => $request->remarks,
            'status' => 'pending',
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
        ]);

        return redirect()->route('document-registry.show', $entry)
            ->with('success', 'Document registration submitted for approval.');
    }
    public function show(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // Check permissions
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403);
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
            'document_no' => 'required|string|max:255|unique:document_registration_entries,document_no,' . $documentRegistrationEntry->id,
            'revision_no' => 'required|string|max:10',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'customer' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $documentRegistrationEntry->update($request->only([
            'document_title', 'document_no', 'revision_no', 'device_name',
            'originator_name', 'customer', 'remarks'
        ]));

        return redirect()->route('document-registry.show', $documentRegistrationEntry)
            ->with('success', 'Document registration entry updated successfully.');
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
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $documentRegistrationEntry->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
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
            'revision_notes' => 'required|string|max:1000',
        ]);

        // Since you only have 3 statuses, we'll reject with revision notes
        $documentRegistrationEntry->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'revision_notes' => $request->revision_notes,
            'rejection_reason' => 'Revision required: ' . $request->revision_notes,
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
            ->with('success', 'Document registration submission withdrawn.');
    }

    public function bulkApprove(Request $request)
    {
        if (!Auth::user()->can('bulk approve document registrations')) {
            abort(403);
        }

        $request->validate([
            'entries' => 'required|array',
            'entries.*' => 'exists:document_registration_entries,id',
        ]);

        $count = DocumentRegistrationEntry::whereIn('id', $request->entries)
            ->where('status', 'pending')
            ->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
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
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $count = DocumentRegistrationEntry::whereIn('id', $request->entries)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

        return back()->with('success', "{$count} document registrations rejected.");
    }

    public function reassignApprover(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('reassign document approver')) {
            abort(403);
        }

        $request->validate([
            'approver_id' => 'required|exists:users,id',
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
            'reason' => 'required|string|max:1000',
        ]);

        if ($request->action === 'approve') {
            $documentRegistrationEntry->update([
                'status' => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
                'remarks' => ($documentRegistrationEntry->remarks ? $documentRegistrationEntry->remarks . "\n\n" : '') .
                            "Override Approval: " . $request->reason,
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
               (Auth::user()->can('view pending document registrations') &&
                ($entry->status === 'pending' || $entry->submitted_by === Auth::id())) ||
               $entry->submitted_by === Auth::id();
    }

    private function canEditEntry(DocumentRegistrationEntry $entry)
    {
        return Auth::user()->can('edit document registration details') &&
               $entry->submitted_by === Auth::id() &&
               $entry->status === 'pending';
    }

    public function downloadFile(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403);
        }

        if (!$documentRegistrationEntry->hasFile()) {
            abort(404, 'File not found');
        }

        return Storage::disk('local')->download( // Specify the disk explicitly
            $documentRegistrationEntry->file_path,
            $documentRegistrationEntry->original_filename
        );
    }

    public function previewFile(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403);
        }

        if (!$documentRegistrationEntry->hasFile()) {
            abort(404, 'File not found');
        }

        // Use the private disk instead of building the path manually
        if (!Storage::disk('private')->exists($documentRegistrationEntry->file_path)) {
            abort(404, 'File not found');
        }

        $filePath = Storage::disk('private')->path($documentRegistrationEntry->file_path);

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
        ]);
    }

    public function previewApi(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied'
            ], 403);
        }

        if (!$documentRegistrationEntry->hasFile()) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        // Check if it's a Word document
        if (!str_contains($documentRegistrationEntry->mime_type, 'word') &&
            !str_contains($documentRegistrationEntry->mime_type, 'document')) {
            return response()->json([
                'success' => false,
                'message' => 'Preview not available for this file type'
            ], 400);
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

            // Try to load the document
            try {
                $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document appears to be corrupted or uses an unsupported format'
                ], 400);
            }

            // Convert to HTML
            try {
                $htmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'HTML');

                $tempFile = tempnam(sys_get_temp_dir(), 'phpword_preview_');
                $htmlWriter->save($tempFile);

                $htmlContent = file_get_contents($tempFile);
                unlink($tempFile);

                if (empty($htmlContent)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Document appears to be empty or could not be converted'
                    ], 400);
                }

                // Clean up the HTML content
                $htmlContent = $this->cleanWordHtml($htmlContent);

                return response()->json([
                    'success' => true,
                    'content' => $htmlContent
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error converting document to preview format'
                ], 500);
            }

        } catch (\Exception $e) {
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
