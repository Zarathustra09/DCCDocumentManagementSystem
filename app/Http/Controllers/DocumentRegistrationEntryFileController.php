<?php

namespace App\Http\Controllers;

use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\DocumentRegistrationEntryFileStatus;
use App\Models\User;
use App\Notifications\DocumentRegistryFileCreated;
use App\Notifications\DocumentRegistryFileStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentRegistrationEntryFileController extends Controller
{
    public function approve(Request $request, $id)
    {
        $file = DocumentRegistrationEntryFile::findOrFail($id);
        if (!auth()->user()->can('approve document registration') || $file->status->name !== 'Pending') {
            abort(403);
        }

        $implementedStatus = DocumentRegistrationEntryFileStatus::where('name', 'Implemented')->first();
        $entryImplementedStatus = \App\Models\DocumentRegistrationEntryStatus::where('name', 'Implemented')->first();

        $file->update([
            'status_id' => $implementedStatus->id,
            'implemented_by' => auth()->id(),
            'implemented_at' => now(),
            'rejection_reason' => null,
        ]);

        $file->registrationEntry->update([
            'status_id' => $entryImplementedStatus->id,
            'implemented_by' => auth()->id(),
            'implemented_at' => now(),
        ]);

        $user = $file->registrationEntry->submittedBy;
        if ($user) {
            $user->notify(new DocumentRegistryFileStatusUpdated($file, $file->status->name));
        }

        return back()->with('success', 'File approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $file = DocumentRegistrationEntryFile::findOrFail($id);
        if (!auth()->user()->can('reject document registration') || $file->status->name !== 'Pending') {
            abort(403);
        }
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        // Use 'Returned' status for files instead of 'Cancelled'
        $returnedStatus = DocumentRegistrationEntryFileStatus::where('name', 'Returned')->first();

        // Add error handling if status doesn't exist
        if (!$returnedStatus) {
            return back()->withErrors(['error' => 'File status "Returned" not found. Please contact administrator.']);
        }

        $file->update([
            'status_id' => $returnedStatus->id,
            'implemented_by' => auth()->id(),
            'implemented_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $user = $file->registrationEntry->submittedBy;
        if ($user) {
            $user->notify(new DocumentRegistryFileStatusUpdated($file, $file->status->name));
        }

        return back()->with('success', 'File returned for revision.');
    }

    public function uploadFile(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('submit document for approval') || $documentRegistrationEntry->status->name !== 'Pending') {
            abort(403);
        }

        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,txt,xls,xlsx,csv|max:10240'
        ]);

        $file = $request->file('document_file');
        $pendingStatus = DocumentRegistrationEntryFileStatus::where('name', 'Pending')->first();

        DocumentRegistrationEntryFile::create([
            'entry_id' => $documentRegistrationEntry->id,
            'file_path' => $file->store('document_registrations', 'local'),
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status_id' => $pendingStatus->id,
        ]);

        $file = DocumentRegistrationEntryFile::where('entry_id', $documentRegistrationEntry->id)
            ->latest('id')->first();

        return back()->with('success', 'File uploaded successfully.');
    }

    public function preview(Request $request, $id)
    {
        $file = DocumentRegistrationEntryFile::findOrFail($id);

        // Check if the user can view the parent entry
        $entry = $file->registrationEntry;
        if (!auth()->user()->can('view all document registrations') &&
            (!auth()->user()->can('view own document registrations') || $entry->submitted_by !== auth()->id())) {
            abort(403, 'You do not have permission to view this file.');
        }

        if (!$file || !Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        $filePath = Storage::disk('local')->path($file->file_path);

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

    public function download(Request $request, $id)
    {
        $file = DocumentRegistrationEntryFile::findOrFail($id);
        $entry = $file->registrationEntry;

        if (!auth()->user()->can('view all document registrations') &&
            (!auth()->user()->can('view own document registrations') || $entry->submitted_by !== auth()->id())) {
            abort(403, 'You do not have permission to download this file.');
        }

        if (!$file || !Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download(
            $file->file_path,
            $file->original_filename
        );
    }
}
