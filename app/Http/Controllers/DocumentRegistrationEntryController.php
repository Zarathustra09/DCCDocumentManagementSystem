<?php

namespace App\Http\Controllers;

use App\DataTables\RegistrationsDataTable;
use App\DataTables\UserRegistrationsDataTable;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Customer;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\DocumentRegistrationEntryStatus;
use App\Models\MainCategory;
use App\Models\User;
use App\Notifications\DocumentRegistryEntryCreated;
use App\Notifications\DocumentRegistryEntryStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Exports\DocumentRegistryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB; // added DB facade

class DocumentRegistrationEntryController extends Controller
{
    public function index(UserRegistrationsDataTable $dataTable)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return $dataTable->render('document-registry.index', compact('categories'));
    }

    public function create()
    {
        if (!Auth::user()->can('submit document for approval')) {
            abort(403, 'You do not have permission to submit documents for approval.');
        }

        $mainCategories = MainCategory::with(['subcategories' => function ($query) {
            $query->where('is_active', true)->orderBy('name');
        }])->orderBy('name')->get();

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('document-registry.create', compact('mainCategories', 'customers'));
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
            'category_id' => 'required|exists:subcategories,id',
            'customer_id' => 'nullable|exists:customers,id',
            'revision_no' => 'nullable|string|max:50',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            // increased max from 10240 KB (10MB) to 20480 KB (20MB)
            'document_file' => 'required|file|mimes:pdf,doc,docx,txt,xls,xlsx,csv|max:20480'
        ];


        $request->validate($rules);

        $pendingStatus = DocumentRegistrationEntryStatus::where('name', 'Pending')->first();

        DB::beginTransaction();
        try {
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

            // Refresh the entry to ensure all data is loaded
            $entry->refresh();

            DB::commit();

            // Send notification (make sure this doesn't interfere with routing)
            try {
                DocumentRegistryEntryCreated::sendToAdmins($entry);
            } catch (\Exception $e) {
                Log::error('Failed to send notification: ' . $e->getMessage());
            }

            return redirect()->route('document-registry.show', ['documentRegistrationEntry' => $entry->id])
                ->with('success', 'Document registration submitted successfully and is pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create document registration entry: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit document registration. Please try again.');
        }
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

        $mainCategories = MainCategory::with(['subcategories' => function ($query) {
            $query->where('is_active', true)->orderBy('name');
        }])->orderBy('name')->get();

        $categories = SubCategory::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('document-registry.edit', compact('documentRegistrationEntry', 'mainCategories', 'categories', 'customers'));
    }



    public function approve(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('approve document registration') ||
            $documentRegistrationEntry->status->name !== 'Pending') {
            abort(403);
        }

        $implementedStatus = DocumentRegistrationEntryStatus::where('name', 'Implemented')->first();
        $implementedFileStatus = \App\Models\DocumentRegistrationEntryFileStatus::where('name', 'Implemented')->first();

        DB::beginTransaction();
        try {
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

            DB::commit();

            $user = $documentRegistrationEntry->submittedBy;
            if ($user) {
                $user->notify(new DocumentRegistryEntryStatusUpdated($documentRegistrationEntry, $documentRegistrationEntry->status));
            }


            return back()->with('success', 'Document registration approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve document registration. Please try again.');
        }
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

        DB::beginTransaction();
        try {
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

            DB::commit();

            $user = $documentRegistrationEntry->submittedBy;
            if ($user) {
                $user->notify(new DocumentRegistryEntryStatusUpdated($documentRegistrationEntry, $documentRegistrationEntry->status));
            }

            return back()->with('success', 'Document registration rejected.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject document registration. Please try again.');
        }
    }

    public function update(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!$this->canEditEntry($documentRegistrationEntry)) {
            abort(403);
        }

        // Detect if this is a minimal update (only category/customer from DCN modal)
        $isMinimalUpdate = $request->filled('category_id') &&
                          $request->filled('customer_id') &&
                          !$request->filled('document_title');

        if ($isMinimalUpdate) {
            // Minimal validation for category/customer updates from DCN modal
            $request->validate([
                'category_id' => 'required|exists:subcategories,id',
                'customer_id' => 'required|exists:customers,id',
            ]);

            DB::beginTransaction();
            try {
                $documentRegistrationEntry->update([
                    'category_id' => $request->category_id,
                    'customer_id' => $request->customer_id,
                ]);

                DB::commit();

                // Return JSON response for AJAX requests
                if ($request->wantsJson() || $request->ajax()) {
                    $documentRegistrationEntry->load(['category', 'customer']);
                    return response()->json([
                        'success' => true,
                        'message' => 'Category and customer updated successfully.',
                        'entry' => [
                            'id' => $documentRegistrationEntry->id,
                            'category_id' => $documentRegistrationEntry->category_id,
                            'customer_id' => $documentRegistrationEntry->customer_id,
                            'category' => $documentRegistrationEntry->category ? [
                                'id' => $documentRegistrationEntry->category->id,
                                'name' => $documentRegistrationEntry->category->name,
                                'code' => $documentRegistrationEntry->category->code
                            ] : null,
                            'customer' => $documentRegistrationEntry->customer ? [
                                'id' => $documentRegistrationEntry->customer->id,
                                'name' => $documentRegistrationEntry->customer->name,
                                'code' => $documentRegistrationEntry->customer->code
                            ] : null,
                        ]
                    ]);
                }

                return redirect()->route('document-registry.show', $documentRegistrationEntry)
                    ->with('success', 'Category and customer updated successfully.');
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Minimal update failed: ' . $e->getMessage());
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update category/customer'
                    ], 500);
                }
                return back()->with('error', 'Failed to update category and customer. Please try again.');
            }
        }

        // Full update validation
        $request->validate([
            'document_title' => 'required|string|max:255',
            'category_id' => 'required|exists:subcategories,id',
            'customer_id' => 'nullable|exists:customers,id',
            'revision_no' => 'nullable|string|max:50',
            'device_name' => 'nullable|string|max:255',
            'document_no' => 'nullable|string|max:100',
            'originator_name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Determine whether current user may update the document number
            $canEditDocNo = Auth::user()->can('edit document registration details');

            $allowedFields = [
                'document_title', 'category_id', 'customer_id', 'revision_no', 'device_name', 'document_no',
                'originator_name', 'remarks'
            ];

            if (! $canEditDocNo) {
                $allowedFields = array_diff($allowedFields, ['document_no']);
            }

            $documentRegistrationEntry->update($request->only($allowedFields));

            DB::commit();

            return redirect()->route('document-registry.show', $documentRegistrationEntry)
                ->with('success', 'Document registration updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Full update failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to update document registration. Please try again.');
        }
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

    public function list(RegistrationsDataTable $dataTable)
    {
        $submitters = User::whereIn('id', DocumentRegistrationEntry::distinct()->pluck('submitted_by'))
            ->get()
            ->sortBy('name')
            ->values();

        $statuses = DocumentRegistrationEntryStatus::active()
            ->orderBy('name')
            ->get();

        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();

        $pendingCount = DocumentRegistrationEntry::whereHas('status', fn($q) => $q->where('name', 'Pending'))->count();
        $approvedCount = DocumentRegistrationEntry::whereHas('status', fn($q) => $q->where('name', 'Implemented'))->count();
        $rejectedCount = DocumentRegistrationEntry::whereHas('status', fn($q) => $q->where('name', 'Cancelled'))->count();

        return $dataTable->render('document-registry.list', compact(
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

        DB::beginTransaction();
        try {
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

            DB::commit();

            return back()->with('success', 'Revision requested for document registration.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Require revision failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to request revision. Please try again.');
        }
    }


    private function canViewEntry(DocumentRegistrationEntry $entry)
    {
        return Auth::user()->can('view all document registrations')
            || (Auth::user()->can('view own document registrations') && $entry->submitted_by === Auth::id())
            || ($entry->submitted_by === Auth::id());
    }

    private function canEditEntry(DocumentRegistrationEntry $entry)
    {
        return (Auth::user()->can('edit document registration details') || $entry->submitted_by === Auth::id() && $entry->status->name === 'Pending')
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
}
