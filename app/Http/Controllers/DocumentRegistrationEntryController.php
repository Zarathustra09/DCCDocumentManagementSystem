<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\DocumentRegistrationEntryStatus;
use App\Models\User;
use App\Notifications\DocumentRegistryEntryCreated;
use App\Notifications\DocumentRegistryEntryStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Exports\DocumentRegistryExport;
use Maatwebsite\Excel\Facades\Excel;

class DocumentRegistrationEntryController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRegistrationEntry::with(['submittedBy', 'approvedBy', 'status', 'category']);

        $query->where('submitted_by', Auth::id());

        if ($request->has('status') && $request->status !== '' && $request->status !== null) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        if ($request->has('category_id') && $request->category_id !== '' && $request->category_id !== null) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_no', 'like', "%{$search}%")
                    ->orWhere('document_title', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%")
                    ->orWhere('originator_name', 'like', "%{$search}%")
                    ->orWhereHas('category', function($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        $entries = $query->latest()->paginate(15);
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('document-registry.index', compact('entries', 'categories'));
    }

    public function create()
    {
        if (!Auth::user()->can('submit document for approval')) {
            abort(403, 'You do not have permission to submit documents for approval.');
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('document-registry.create', compact('categories', 'customers'));
    }

    public function store(Request $request)
    {
        if (!Auth::user()->can('submit document for approval')) {
            abort(403);
        }

        // Basic rules (customer rule set conditionally below)
        $rules = [
            'document_no' => 'nullable|string|max:100',
            'document_title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            // 'customer_id' rule set after we detect category
            'revision_no' => 'nullable|string|max:50',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,txt,xls,xlsx,csv|max:10240'
        ];

        // Detect if selected category is an in-house SPI type (allow null customer)
        $category = Category::find($request->input('category_id'));
        $isInHouseSPI = false;
        if ($category) {
            $code = strtolower($category->code ?? '');
            $name = strtolower($category->name ?? '');
            $isInHouseSPI = ($code === 'cn2') || str_contains($name, 'spi') || str_contains($name, 'in-house');
        }
        $rules['customer_id'] = $isInHouseSPI ? 'nullable|exists:customers,id' : 'required|exists:customers,id';

        $request->validate($rules);

        $pendingStatus = DocumentRegistrationEntryStatus::where('name', 'Pending')->first();

        $entry = DocumentRegistrationEntry::create([
            'document_no' => $request->document_no,
            'document_title' => $request->document_title,
            'category_id' => $request->category_id,
            'customer_id' => $request->customer_id,
            'revision_no' => $request->revision_no,
            'device_name' => $request->device_name,
            'originator_name' => $request->originator_name,
//            'customer' => $request->customer,
            'remarks' => $request->remarks,
            'status_id' => $pendingStatus->id,
            'submitted_by' => Auth::id(),
            'submitted_at' => now(),
        ]);

        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $pendingFileStatus = \App\Models\DocumentRegistrationEntryFileStatus::where('name', 'Pending')->first();

            DocumentRegistrationEntryFile::create([
                'entry_id' => $entry->id,
                'file_path' => $file->store('document_registrations', 'local'),
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'status_id' => $pendingFileStatus->id,
            ]);
        }

        DocumentRegistryEntryCreated::sendToAdmins($entry);

        return redirect()->route('document-registry.show', $entry)
            ->with('success', 'Document registration submitted successfully and is pending approval.');
    }

    public function show(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403, 'You do not have permission to view this document registration.');
        }
        $documentRegistrationEntry->load(['submittedBy', 'approvedBy', 'documents', 'files.status', 'status', 'category']);
        return view('document-registry.show', compact('documentRegistrationEntry'));
    }

    public function edit(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canEditEntry($documentRegistrationEntry)) {
            abort(403);
        }

        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('document-registry.edit', compact('documentRegistrationEntry', 'categories', 'customers'));
    }



    public function approve(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('approve document registration') ||
            $documentRegistrationEntry->status->name !== 'Pending') {
            abort(403);
        }

        $implementedStatus = DocumentRegistrationEntryStatus::where('name', 'Implemented')->first();
        $implementedFileStatus = \App\Models\DocumentRegistrationEntryFileStatus::where('name', 'Implemented')->first();

        $documentRegistrationEntry->update([
            'status_id' => $implementedStatus->id,
            'implemented_by' => Auth::id(),
            'implemented_at' => now(),
            'rejection_reason' => null,
            'revision_notes' => null,
        ]);

        $documentRegistrationEntry->files()->update([
            'status_id' => $implementedFileStatus->id,
            'implemented_by' => Auth::id(),
            'implemented_at' => now(),
            'rejection_reason' => null,
        ]);

        $documentRegistrationEntry->refresh();

        $user = $documentRegistrationEntry->submittedBy;
        if ($user) {
            $user->notify(new DocumentRegistryEntryStatusUpdated($documentRegistrationEntry, $documentRegistrationEntry->status));
        }


        return back()->with('success', 'Document registration approved successfully.');
    }

    public function reject(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('reject document registration') ||
            $documentRegistrationEntry->status->name !== 'Pending') {
            abort(403);
        }
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $cancelledStatus = DocumentRegistrationEntryStatus::where('name', 'Cancelled')->first();
        $returnedFileStatus = \App\Models\DocumentRegistrationEntryFileStatus::where('name', 'Returned')->first();

        $documentRegistrationEntry->update([
            'status_id' => $cancelledStatus->id,
            'implemented_by' => Auth::id(),
            'implemented_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'revision_notes' => null,
        ]);

        // Return all existing files for this entry
        $documentRegistrationEntry->files()->update([
            'status_id' => $returnedFileStatus->id,
            'implemented_by' => Auth::id(),
            'implemented_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $documentRegistrationEntry->refresh();

        $user = $documentRegistrationEntry->submittedBy;
        if ($user) {
            $user->notify(new DocumentRegistryEntryStatusUpdated($documentRegistrationEntry, $documentRegistrationEntry->status));
        }

        return back()->with('success', 'Document registration rejected.');
    }

    public function update(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canEditEntry($documentRegistrationEntry)) {
            abort(403);
        }

        $request->validate([
            'document_title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'customer_id' => 'nullable|exists:customers,id',
            'revision_no' => 'nullable|string|max:50',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $documentRegistrationEntry->update($request->only([
            'document_title', 'category_id', 'customer_id', 'revision_no', 'device_name',
            'originator_name', 'remarks'
        ]));

        return redirect()->route('document-registry.show', $documentRegistrationEntry)
            ->with('success', 'Document registration updated successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $page = $request->get('page', 1);
        $perPage = 10;

        $entries = DocumentRegistrationEntry::with('category')
            ->whereHas('status', function($q) {
                $q->where('name', 'Implemented');
            })
            ->where(function($q) use ($query) {
                $q->where('document_no', 'like', "%{$query}%")
                    ->orWhere('document_title', 'like', "%{$query}%")
                    ->orWhere('device_name', 'like', "%{$query}%")
                    ->orWhere('originator_name', 'like', "%{$query}%")
                    ->orWhereHas('category', function($categoryQuery) use ($query) {
                        $categoryQuery->where('name', 'like', "%{$query}%")
                            ->orWhere('code', 'like', "%{$query}%");
                    });
            })
            ->orderBy('document_title')
            ->paginate($perPage, ['*'], 'page', $page);

        $morePages = $entries->hasMorePages();
        $results = [];

        foreach ($entries as $entry) {
            $categoryInfo = $entry->category ? " [{$entry->category->code}]" : "";
            $results[] = [
                'id' => $entry->id,
                'text' => "{$entry->document_no} - {$entry->document_title}" .
                    ($entry->device_name ? " ({$entry->device_name})" : "") . $categoryInfo
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
        $query = DocumentRegistrationEntry::with(['submittedBy', 'approvedBy', 'status', 'category']);

        if (Auth::user()->can('view all document registrations')) {
            // User can view all entries
        } else {
            // Restrict to user's own entries
            $query->where('submitted_by', Auth::id());
        }

        // Status filter using relationship
        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', $request->status);
            });
        }

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_title', 'like', "%{$search}%")
                    ->orWhere('document_no', 'like', "%{$search}%")
                    ->orWhere('originator_name', 'like', "%{$search}%")
                    ->orWhere('customer', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%")
                    ->orWhereHas('category', function($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        // Submitted by filter
        if ($request->filled('submitted_by')) {
            $query->where('submitted_by', $request->submitted_by);
        }

        // Date range filters
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $entries = $query->latest('submitted_at')->get();

        // Calculate counts using relationships
        $pendingCount = DocumentRegistrationEntry::whereHas('status', function ($q) {
            $q->where('name', 'Pending');
        })->count();

        $approvedCount = DocumentRegistrationEntry::whereHas('status', function ($q) {
            $q->where('name', 'Implemented');
        })->count();

        $rejectedCount = DocumentRegistrationEntry::whereHas('status', function ($q) {
            $q->where('name', 'Cancelled');
        })->count();

        // Get filter options
        $submitters = User::whereIn('id', DocumentRegistrationEntry::distinct()->pluck('submitted_by'))
            ->get()
            ->sortBy('name')
            ->values();

        // Get status options from the relationship
        $statuses = DocumentRegistrationEntryStatus::active()
            ->orderBy('name')
            ->get();

        // Get categories for filtering
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('document-registry.list', compact(
            'entries',
            'submitters',
            'statuses',
            'categories',
            'pendingCount',
            'approvedCount',
            'rejectedCount'
        ));
    }

    public function requireRevision(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('require revision for document') ||
            $documentRegistrationEntry->status->name !== 'Pending') {
            abort(403);
        }
        $request->validate([
            'revision_notes' => 'required|string'
        ]);

        $cancelledStatus = DocumentRegistrationEntryStatus::where('name', 'Cancelled')->first();
        $cancelledFileStatus = \App\Models\DocumentRegistrationEntryFileStatus::where('name', 'Cancelled')->first();

        $documentRegistrationEntry->update([
            'status_id' => $cancelledStatus->id,
            'implemented_by' => Auth::id(),
            'implemented_at' => now(),
            'revision_notes' => $request->revision_notes,
            'rejection_reason' => 'Revision required. Please see revision notes.',
        ]);

        $documentRegistrationEntry->files()->update([
            'status_id' => $cancelledFileStatus->id,
            'implemented_by' => Auth::id(),
            'implemented_at' => now(),
            'rejection_reason' => 'Revision required. Please see revision notes.',
        ]);

        $documentRegistrationEntry->refresh();

        return back()->with('success', 'Revision requested for document registration.');
    }

    public function withdraw(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('withdraw document submission') ||
            $documentRegistrationEntry->submitted_by !== Auth::id() ||
            $documentRegistrationEntry->status->name !== 'Pending') {
            abort(403);
        }
        $documentRegistrationEntry->files()->delete();
        $documentRegistrationEntry->delete();
        return redirect()->route('document-registry.index')
            ->with('success', 'Document registration withdrawn successfully.');
    }

    private function canViewEntry(DocumentRegistrationEntry $entry)
    {
        return Auth::user()->can('view all document registrations')
            || (Auth::user()->can('view own document registrations') && $entry->submitted_by === Auth::id())
            || ($entry->submitted_by === Auth::id());
    }

    private function canEditEntry(DocumentRegistrationEntry $entry)
    {
        return (Auth::user()->can('edit document registration details') && $entry->submitted_by === Auth::id() && $entry->status->name === 'Pending')
            || ($entry->submitted_by === Auth::id() && $entry->status->name === 'Pending');
    }

    public function download(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403, 'You do not have permission to download this file.');
        }

        $fileId = request('file_id');
        $file = $documentRegistrationEntry->files()->find($fileId);

        if (!$file || !Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download(
            $file->file_path,
            $file->original_filename
        );
    }

    public function preview(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canViewEntry($documentRegistrationEntry)) {
            abort(403, 'You do not have permission to view this file.');
        }
        $fileId = request('file_id');
        $file = $documentRegistrationEntry->files()->find($fileId);
        if (!$file || !\Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }
        $filePath = \Storage::disk('local')->path($file->file_path);
        if (str_contains($file->mime_type, 'pdf') || str_contains($file->mime_type, 'image')) {
            return response()->file($filePath, [
                'Content-Type' => $file->mime_type,
                'Content-Disposition' => 'inline; filename="' . $file->original_filename . '"'
            ]);
        }
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
        $fileId = request('file_id');
        $file = $documentRegistrationEntry->files()->find($fileId);
        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'No file attached'
            ], 404);
        }
        try {
            $filePath = Storage::disk('local')->path($file->file_path);
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
            $extension = strtolower(pathinfo($file->original_filename, PATHINFO_EXTENSION));
            $mimeType = $file->mime_type;
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
            $spreadsheet = IOFactory::load($filePath);
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
