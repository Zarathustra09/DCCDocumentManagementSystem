<?php

namespace App\Http\Controllers;

use App\Models\DocumentRegistrationEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
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
        ]);

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
}
