<?php

namespace App\Http\Controllers;

use App\Exports\DocumentRegistryExport;
use App\Models\DocumentRegistrationEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExportController extends Controller
{
    public function export(Request $request)
    {
        // Apply same filters as list method
        $query = DocumentRegistrationEntry::with(['submittedBy', 'approvedBy']);

        if (Auth::user()->can('view all document registrations')) {
            // User can view all entries
        } else {
            // Restrict to user's own entries
            $query->where('submitted_by', Auth::id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_no', 'like', "%{$search}%")
                    ->orWhere('document_title', 'like', "%{$search}%")
                    ->orWhere('device_name', 'like', "%{$search}%")
                    ->orWhere('originator_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('customer')) {
            $query->where('customer', 'like', "%{$request->customer}%");
        }

        if ($request->filled('device_name')) {
            $query->where('device_name', 'like', "%{$request->device_name}%");
        }

        if ($request->filled('submitted_by')) {
            $query->where('submitted_by', $request->submitted_by);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('submitted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('submitted_at', '<=', $request->date_to);
        }

        $entries = $query->latest('submitted_at')->get();

        $filename = 'document_registry_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new DocumentRegistryExport($entries), $filename);
    }
}
