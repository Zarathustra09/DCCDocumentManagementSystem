<?php

namespace App\Http\Controllers\API\SMTS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DocumentRegistrationEntry;

class DocumentRegistryEntryController extends Controller
{
    /**
     * GET /smts/document-registry-entry
     * Return paginated document registry entries with optional search.
     *
     * Query params:
     * - api_key or header x-api-key : required, compared against env('API_KEY')
     * - q : optional search term (title, document_no, device_name, originator_name)
     * - per_page : optional items per page (default 15, max 100)
     * - page : optional page number (handled by paginator)
     */
    public function index(Request $request)
    {
        // API key validation using env('API_KEY')
        $expected = env('API_KEY');
        $provided = $request->header('x-api-key') ?? $request->query('api_key');

        if (empty($expected) || $provided !== $expected) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Pagination
        $perPage = (int) $request->query('per_page', 15);
        $perPage = $perPage < 1 ? 1 : ($perPage > 100 ? 100 : $perPage);

        // Search
        $search = trim((string) $request->query('q', ''));

        $query = DocumentRegistrationEntry::with([
            'category',
            'customer',
            'status',
            'submittedBy',
            'approvedBy',
            'files'
        ]);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('document_title', 'like', '%' . $search . '%')
                  ->orWhere('document_no', 'like', '%' . $search . '%')
                  ->orWhere('device_name', 'like', '%' . $search . '%')
                  ->orWhere('originator_name', 'like', '%' . $search . '%');
            });
        }

        // Optional: filter by status name or id
        if ($request->filled('status')) {
            $status = $request->query('status');
            if (is_numeric($status)) {
                $query->where('status_id', (int) $status);
            } else {
                $query->whereHas('status', function ($q) use ($status) {
                    $q->where('name', $status);
                });
            }
        }

        $items = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($items);
    }

    // Example resource stubs (implement as needed)
    public function show($id)
    {
        $item = DocumentRegistrationEntry::with(['category','customer','status','submittedBy','approvedBy','files'])
            ->findOrFail($id);
        return response()->json($item);
    }

    public function store(Request $request)
    {
        // Implement create logic if needed
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function update(Request $request, $id)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }

    public function destroy($id)
    {
        return response()->json(['message' => 'Not implemented'], 501);
    }
}
