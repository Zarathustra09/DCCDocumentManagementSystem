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
        // Base query with eager relations
        $query = DocumentRegistrationEntry::with(['submittedBy', 'approvedBy', 'status', 'category', 'customer']);

        // Permission scoping: if user cannot view all, restrict to their entries
        if (!Auth::user()?->can('view all document registrations')) {
            $query->where('submitted_by', Auth::id());
        }

        // Determine advanced flag (supports '1','true',boolean)
        $advanced = filter_var($request->get('advanced', false), FILTER_VALIDATE_BOOLEAN);

        // Apply advanced filters when enabled
        if ($advanced) {
            $status = $request->get('status');
            if ($status !== null && $status !== '') {
                if (is_array($status)) {
                    $query->whereHas('status', fn($q) => $q->whereIn('name', $status));
                } else {
                    $query->whereHas('status', fn($q) => $q->where('name', $status));
                }
            }

            $category = $request->get('category_id');
            if ($category !== null && $category !== '') {
                if (is_array($category)) {
                    $query->whereIn('category_id', $category);
                } else {
                    $query->where('category_id', $category);
                }
            }

            $submittedBy = $request->get('submitted_by');
            if ($submittedBy !== null && $submittedBy !== '') {
                if (is_array($submittedBy)) {
                    $query->whereIn('submitted_by', $submittedBy);
                } else {
                    $query->where('submitted_by', $submittedBy);
                }
            }

            $dateFrom = $request->get('date_from');
            if ($dateFrom) {
                $query->whereDate('submitted_at', '>=', $dateFrom);
            }

            $dateTo = $request->get('date_to');
            if ($dateTo) {
                $query->whereDate('submitted_at', '<=', $dateTo);
            }
        }

        // Global / simple search - apply regardless of advanced flag when provided
        $search = $request->get('search');
        if ($search !== null && $search !== '') {
            $searchStr = is_array($search) ? implode(' ', $search) : $search;
            $searchStr = trim($searchStr);
            if ($searchStr !== '') {
                $query->where(function ($q) use ($searchStr) {
                    $q->where('control_no', 'like', "%{$searchStr}%")
                        ->orWhere('document_no', 'like', "%{$searchStr}%")
                        ->orWhere('document_title', 'like', "%{$searchStr}%")
                        ->orWhere('device_name', 'like', "%{$searchStr}%")
                        ->orWhere('originator_name', 'like', "%{$searchStr}%")
                        ->orWhereHas('category', fn($cq) => $cq->where('name', 'like', "%{$searchStr}%")->orWhere('code', 'like', "%{$searchStr}%"))
                        ->orWhereHas('status', fn($sq) => $sq->where('name', 'like', "%{$searchStr}%"))
                        ->orWhereHas('customer', fn($cust) => $cust->where('name', 'like', "%{$searchStr}%"));
                });
            }
        }

        // Changed to oldest first by submitted_at, with id as a deterministic tiebreaker
        $entries = $query->orderBy('submitted_at', 'asc')->orderBy('id', 'asc')->get();

        $filename = 'document_registry_export_' . now()->format('Y_m_d_H_i_s') . '.xlsx';

        return Excel::download(new DocumentRegistryExport($entries), $filename);
    }
}
