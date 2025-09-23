<?php

namespace App\Http\Controllers;

use App\Exports\DcnFilteredExport;
use App\Models\DocumentRegistrationEntry;
use App\Models\Customer;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DcnController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentRegistrationEntry::with(['customer', 'category', 'submittedBy.department', 'status']);

        // Apply filters based on request parameters
        if ($request->filled('dcn_status')) {
            if ($request->dcn_status === 'with_dcn') {
                $query->whereNotNull('dcn_no');
            } elseif ($request->dcn_status === 'without_dcn') {
                $query->whereNull('dcn_no');
            }
        }

        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_title', 'like', "%{$search}%")
                    ->orWhere('document_no', 'like', "%{$search}%")
                    ->orWhere('dcn_no', 'like', "%{$search}%")
                    ->orWhere('originator_name', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $entries = $query->orderBy('created_at', 'desc')->paginate(15);

        // Preserve query parameters in pagination links
        $entries->appends($request->query());

        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return view('dcn_control.index', compact('entries', 'customers', 'categories'));
    }

    public function updateDcnNumber(Request $request, DocumentRegistrationEntry $entry)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'customer_id' => 'required|exists:customers,id',
            'dcn_suffix' => 'required|string|size:3|regex:/^[0-9]{3}$/',
        ]);

        DB::beginTransaction();

        try {
            // Get category and customer codes
            $category = Category::findOrFail($request->category_id);
            $customer = Customer::findOrFail($request->customer_id);
            $currentYear = date('y'); // 2-digit year (e.g., 25 for 2025)

            // Generate DCN number: CategoryCode + Year + CustomerCode + Suffix
            // Format: CNA25-ALL-001
            $dcnNumber = $category->code . $currentYear . '-' . $customer->code . '-' . $request->dcn_suffix;

            // Check if DCN number already exists (excluding current entry)
            $existingEntry = DocumentRegistrationEntry::where('dcn_no', $dcnNumber)
                ->where('id', '!=', $entry->id)
                ->first();

            if ($existingEntry) {
                return response()->json([
                    'success' => false,
                    'message' => 'This DCN number already exists. Please use a different 3-digit suffix.',
                    'errors' => ['dcn_suffix' => ['DCN number already exists']]
                ], 422);
            }

            // Update the entry with new DCN number and related fields
            $entry->update([
                'dcn_no' => $dcnNumber,
                'category_id' => $request->category_id,
                'customer_id' => $request->customer_id,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'DCN number updated successfully.',
                'dcn_number' => $dcnNumber
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the DCN number.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function generateDcnPreview(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'customer_id' => 'required|exists:customers,id',
            'dcn_suffix' => 'required|string|size:3|regex:/^[0-9]{3}$/',
            'entry_id' => 'nullable|exists:document_registration_entries,id',
        ]);

        try {
            $category = Category::findOrFail($request->category_id);
            $customer = Customer::findOrFail($request->customer_id);
            $currentYear = date('y'); // 2-digit year

            // Generate DCN number preview
            $dcnNumber = $category->code . $currentYear . '-' . $customer->code . '-' . $request->dcn_suffix;

            // Check if DCN number already exists (excluding current entry if provided)
            $query = DocumentRegistrationEntry::where('dcn_no', $dcnNumber);

            if ($request->entry_id) {
                $query->where('id', '!=', $request->entry_id);
            }

            $exists = $query->exists();

            return response()->json([
                'success' => true,
                'dcn_number' => $dcnNumber,
                'exists' => $exists,
                'message' => $exists ? 'This DCN number already exists!' : 'DCN number is available.',
                'format_breakdown' => [
                    'category_code' => $category->code,
                    'year' => $currentYear,
                    'customer_code' => $customer->code,
                    'suffix' => $request->dcn_suffix
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating DCN preview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bulkUpdateDcn(Request $request)
    {
        $request->validate([
            'entries' => 'required|array',
            'entries.*.id' => 'required|exists:document_registration_entries,id',
            'entries.*.category_id' => 'required|exists:categories,id',
            'entries.*.customer_id' => 'required|exists:customers,id',
            'entries.*.dcn_suffix' => 'required|string|size:3|regex:/^[0-9]{3}$/',
        ]);

        $results = [];
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($request->entries as $entryData) {
                $entry = DocumentRegistrationEntry::findOrFail($entryData['id']);
                $category = Category::findOrFail($entryData['category_id']);
                $customer = Customer::findOrFail($entryData['customer_id']);
                $currentYear = date('y');

                $dcnNumber = $category->code . $currentYear . '-' . $customer->code . '-' . $entryData['dcn_suffix'];

                // Check for duplicates
                $existingEntry = DocumentRegistrationEntry::where('dcn_no', $dcnNumber)
                    ->where('id', '!=', $entry->id)
                    ->first();

                if ($existingEntry) {
                    $errors[] = [
                        'entry_id' => $entry->id,
                        'document_title' => $entry->document_title,
                        'dcn_number' => $dcnNumber,
                        'message' => 'DCN number already exists'
                    ];
                    continue;
                }

                $entry->update([
                    'dcn_no' => $dcnNumber,
                    'category_id' => $entryData['category_id'],
                    'customer_id' => $entryData['customer_id'],
                ]);

                $results[] = [
                    'entry_id' => $entry->id,
                    'document_title' => $entry->document_title,
                    'dcn_number' => $dcnNumber,
                    'status' => 'updated'
                ];
            }

            if (empty($errors)) {
                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => 'All DCN numbers updated successfully.',
                    'results' => $results
                ]);
            } else {
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Some DCN numbers could not be updated due to duplicates.',
                    'results' => $results,
                    'errors' => $errors
                ], 422);
            }

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred during bulk update.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clearDcnNumber(DocumentRegistrationEntry $entry)
    {
        try {
            $entry->update(['dcn_no' => null]);

            return response()->json([
                'success' => true,
                'message' => 'DCN number cleared successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while clearing the DCN number.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(DocumentRegistrationEntry $entry)
    {
        $entry->load(['customer', 'category', 'submittedBy', 'status', 'files']);
        return view('dcn.show', compact('entry'));
    }

   public function getEntryData(DocumentRegistrationEntry $entry)
   {
       try {
           $entry->load(['customer', 'category', 'submittedBy.department']);

           // Generate next available suffix if customer and category exist
           $nextSuffix = null;
           if ($entry->customer_id && $entry->category_id) {
               $nextSuffix = $this->generateNextSuffix($entry->category_id, $entry->customer_id, $entry->id);
           }

           // Prepare department string
           $department = null;
           if ($entry->submittedBy && $entry->submittedBy->department) {
               $department = $entry->submittedBy->department->department;
               if ($entry->submittedBy->department->section) {
                   $department .= ' / ' . $entry->submittedBy->department->section;
               }
           }

           return response()->json([
               'success' => true,
               'entry' => [
                   'id' => $entry->id,
                   'document_title' => $entry->document_title,
                   'customer_id' => $entry->customer_id,
                   'category_id' => $entry->category_id,
                   'customer' => $entry->customer ? [
                       'id' => $entry->customer->id,
                       'name' => $entry->customer->name,
                       'code' => $entry->customer->code
                   ] : null,
                   'category' => $entry->category ? [
                       'id' => $entry->category->id,
                       'name' => $entry->category->name,
                       'code' => $entry->category->code
                   ] : null,
                   'current_dcn' => $entry->dcn_no,
                   'suggested_suffix' => $nextSuffix,
                   // Additional fields for modal
                   'originator_name' => $entry->originator_name,
                   'department' => $department,
                   'submitted_at' => $entry->submitted_at ? $entry->submitted_at->format('Y-m-d H:i') : null,
                   'implemented_at' => $entry->implemented_at ? $entry->implemented_at->format('Y-m-d H:i') : null,
                   'document_no' => $entry->document_no,
                   'revision_no' => $entry->revision_no,
                   'device_name' => $entry->device_name,
               ]
           ]);

       } catch (\Exception $e) {
           return response()->json([
               'success' => false,
               'message' => 'Error loading entry data.',
               'error' => $e->getMessage()
           ], 500);
       }
   }

    private function generateNextSuffix($categoryId, $customerId, $excludeEntryId = null)
    {
        try {
            $category = Category::findOrFail($categoryId);
            $customer = Customer::findOrFail($customerId);
            $currentYear = date('y');

            // Create the exact pattern for THIS specific category-customer-year combination
            // Example patterns:
            // - CN25-ALL-xxx (Category: CN, Year: 25, Customer: ALL)
            // - CN25-BUA-xxx (Category: CN, Year: 25, Customer: BUA)
            // - AGA25-ALL-xxx (Category: AGA, Year: 25, Customer: ALL)
            $pattern = $category->code . $currentYear . '-' . $customer->code . '-';

            // Find ALL existing DCN numbers that match this EXACT pattern
            // This ensures each category-customer combination has its own sequence
            $query = DocumentRegistrationEntry::where('dcn_no', 'like', $pattern . '%');

            // Exclude current entry if we're updating (not creating new)
            if ($excludeEntryId) {
                $query->where('id', '!=', $excludeEntryId);
            }

            $existingDcns = $query->pluck('dcn_no')->toArray();

            // Extract ONLY the 3-digit suffixes from DCNs with this exact pattern
            $existingSuffixes = [];
            foreach ($existingDcns as $dcn) {
                // Match the exact pattern and extract the 3-digit suffix
                if (preg_match('/' . preg_quote($pattern) . '(\d{3})$/', $dcn, $matches)) {
                    $existingSuffixes[] = (int)$matches[1];
                }
            }

            // Find the next available number for THIS category-customer combination
            // Each combination starts fresh from 001
            for ($i = 1; $i <= 999; $i++) {
                if (!in_array($i, $existingSuffixes)) {
                    return str_pad($i, 3, '0', STR_PAD_LEFT);
                }
            }

            // If somehow all 999 numbers are used for this combination (very unlikely)
            return null;

        } catch (\Exception $e) {
            return null;
        }
    }

    public function export(Request $request)
    {

        \Log::info('Exporting DCN filtered data', ['request' => $request->all()]);
        $query = DocumentRegistrationEntry::with(['customer', 'category', 'submittedBy.department', 'status']);

        // Apply filters (same as index)
        if ($request->filled('dcn_status')) {
            if ($request->dcn_status === 'with_dcn') {
                $query->whereNotNull('dcn_no');
            } elseif ($request->dcn_status === 'without_dcn') {
                $query->whereNull('dcn_no');
            }
        }
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('document_title', 'like', "%{$search}%")
                    ->orWhere('document_no', 'like', "%{$search}%")
                    ->orWhere('dcn_no', 'like', "%{$search}%")
                    ->orWhere('originator_name', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $entries = $query->orderBy('created_at', 'desc')->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\DcnFilteredExport($entries),
            'dcn_filtered.xlsx'
        );
    }


}
