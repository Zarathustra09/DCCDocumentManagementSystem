<?php

namespace App\Http\Controllers;

use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentRegistrationEntryFileController extends Controller
{

    public function approve(Request $request, $id)
    {
        $file = \App\Models\DocumentRegistrationEntryFile::findOrFail($id);
        if (!auth()->user()->can('approve document registration') || $file->status !== 'pending') {
            abort(403);
        }
        $file->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => null,
        ]);
        $file->registrationEntry->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        return back()->with('success', 'File approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $file = \App\Models\DocumentRegistrationEntryFile::findOrFail($id);
        if (!auth()->user()->can('reject document registration') || $file->status !== 'pending') {
            abort(403);
        }
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);
        $file->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        $file->registrationEntry->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);
        return back()->with('success', 'File rejected.');
    }

    public function uploadFile(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('submit document for approval') || $documentRegistrationEntry->status !== 'pending') {
            abort(403);
        }

        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,txt,xls,xlsx,csv|max:10240'
        ]);

        $file = $request->file('document_file');
        DocumentRegistrationEntryFile::create([
            'entry_id' => $documentRegistrationEntry->id,
            'file_path' => $file->store('document_registrations', 'local'),
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'status' => 'pending',
        ]);

        return back()->with('success', 'File uploaded successfully.');
    }


    public function preview(Request $request, $id)
    {
        $file = \App\Models\DocumentRegistrationEntryFile::findOrFail($id);

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
        $file = \App\Models\DocumentRegistrationEntryFile::findOrFail($id);
        $entry = $file->registrationEntry;

        if (!auth()->user()->can('view all document registrations') &&
            (!auth()->user()->can('view own document registrations') || $entry->submitted_by !== auth()->id())) {
            abort(403, 'You do not have permission to download this file.');
        }

        if (!$file || !\Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return \Storage::disk('local')->download(
            $file->file_path,
            $file->original_filename
        );
    }
}
