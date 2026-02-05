<?php

namespace App\Http\Controllers;

use App\DataTables\RegistrationsDataTable;
use App\DataTables\UserRegistrationsDataTable;
use App\Interfaces\DocumentRegistryServiceInterface;
use App\Models\Category;
use App\Models\DocumentRegistrationEntryStatus;
use App\Models\SubCategory;
use App\Models\Customer;
use App\Models\DocumentRegistrationEntry;
use App\Models\MainCategory;
use App\Models\User;
use App\Notifications\DocumentRegistryEntryStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Exports\DocumentRegistryExport;
use Maatwebsite\Excel\Facades\Excel;

class DocumentRegistrationEntryController extends Controller
{
    protected DocumentRegistryServiceInterface $documentRegistryService;

    public function __construct(DocumentRegistryServiceInterface $documentRegistryService)
    {
        $this->documentRegistryService = $documentRegistryService;
    }

    public function index(UserRegistrationsDataTable $dataTable)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return $dataTable->render('document-registry.index', compact('categories'));
    }

    public function create()
    {
        // use policy instead of inline Spatie check
        $this->authorize('create', DocumentRegistrationEntry::class);

        $mainCategories = MainCategory::with(['subcategories' => function ($query) {
            $query->where('is_active', true)->orderBy('name');
        }])->orderBy('name')->get();

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('document-registry.create', compact('mainCategories', 'customers'));
    }

    public function store(Request $request)
    {
        // use policy instead of inline Spatie check
        $this->authorize('create', DocumentRegistrationEntry::class);

        try {
            $entry = $this->documentRegistryService->create($request);

            return redirect()->route('document-registry.show', ['documentRegistrationEntry' => $entry->id])
                ->with('success', 'Document registration submitted successfully and is pending approval.');
        } catch (\Exception $e) {
            Log::error('Failed to create document registration entry: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit document registration. Please try again.');
        }
    }

    public function show(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // delegate permission to policy
        $this->authorize('view', $documentRegistrationEntry);

        $documentRegistrationEntry->load(['submittedBy', 'approvedBy', 'documents', 'files.status', 'status', 'category']);
        return view('document-registry.show', compact('documentRegistrationEntry'));
    }

    public function edit(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // use policy authorization (delegates to DocumentRegistrationEntryPolicy@update)
        $this->authorize('update', $documentRegistrationEntry);

        $mainCategories = MainCategory::with(['subcategories' => function ($query) {
            $query->where('is_active', true)->orderBy('name');
        }])->orderBy('name')->get();

        $categories = SubCategory::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        return view('document-registry.edit', compact('documentRegistrationEntry', 'mainCategories', 'categories', 'customers'));
    }



    public function approve(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // use policy authorization (delegates to DocumentRegistrationEntryPolicy@approve)
        $this->authorize('approve', $documentRegistrationEntry);

        try {
            $this->documentRegistryService->approve($request, $documentRegistrationEntry);
            return back()->with('success', 'Document registration approved successfully.');
        } catch (\Exception $e) {
            Log::error('Approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve document registration. Please try again.');
        }
    }

    public function reject(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // use policy authorization (delegates to DocumentRegistrationEntryPolicy@reject)
        $this->authorize('reject', $documentRegistrationEntry);

        try {
            $this->documentRegistryService->reject($request, $documentRegistrationEntry);
            return back()->with('success', 'Document registration rejected.');
        } catch (\Exception $e) {
            Log::error('Rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to reject document registration. Please try again.');
        }
    }

    public function update(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // use policy authorization (delegates to DocumentRegistrationEntryPolicy@update)
        $this->authorize('update', $documentRegistrationEntry);

        $isMinimalUpdate = $request->filled('category_id') &&
                          $request->filled('customer_id') &&
                          !$request->filled('document_title');

        if ($isMinimalUpdate) {
            try {
                $this->documentRegistryService->updateMinimal($request, $documentRegistrationEntry);

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

        try {
            $this->documentRegistryService->updateFull($request, $documentRegistrationEntry);

            return redirect()->route('document-registry.show', $documentRegistrationEntry)
                ->with('success', 'Document registration updated successfully.');
        } catch (\Exception $e) {
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
        // use policy authorization (delegates to DocumentRegistrationEntryPolicy@requireRevision)
        $this->authorize('requireRevision', $documentRegistrationEntry);

        try {
            $this->documentRegistryService->requireRevision($request, $documentRegistrationEntry);
            return back()->with('success', 'Revision requested for document registration.');
        } catch (\Exception $e) {
            Log::error('Require revision failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to request revision. Please try again.');
        }
    }


    public function download(DocumentRegistrationEntry $documentRegistrationEntry)
    {
        // delegate permission to policy
        $this->authorize('view', $documentRegistrationEntry);

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
