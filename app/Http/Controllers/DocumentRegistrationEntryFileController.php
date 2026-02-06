<?php

namespace App\Http\Controllers;

use App\Interfaces\DocumentRegistryFileServiceInterface;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // added DB facade
use Illuminate\Support\Facades\Log; // added Log facade

class DocumentRegistrationEntryFileController extends Controller
{
    private DocumentRegistryFileServiceInterface $documentRegistryFileService;

    public function __construct(DocumentRegistryFileServiceInterface $documentRegistryFileService)
    {
        $this->documentRegistryFileService = $documentRegistryFileService;
    }

    public function approve(Request $request, $id)
    {
        $file = DocumentRegistrationEntryFile::findOrFail($id);
        if (!auth()->user()->can('approve document registration') || $file->status->name !== 'Pending') {
            abort(403);
        }

        $entryId = $file->entry_id;

        DB::beginTransaction();
        try {
            $this->documentRegistryFileService->approve($file);
            DB::commit();

            return redirect()->route('document-registry.show', ['documentRegistrationEntry' => $entryId])
                ->with('success', 'File approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File approval failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve file. Please try again.');
        }
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

        $entryId = $file->entry_id;

        DB::beginTransaction();
        try {
            $this->documentRegistryFileService->reject($file, $request->rejection_reason);
            DB::commit();

            return redirect()->route('document-registry.show', ['documentRegistrationEntry' => $entryId])
                ->with('success', 'File returned for revision.');
        } catch (\RuntimeException $e) {
            DB::rollBack();
            if ($e->getMessage() === 'File status "Returned" not found. Please contact administrator.') {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
            Log::error('File rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to return file. Please try again.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File rejection failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to return file. Please try again.');
        }
    }

    public function uploadFile(Request $request, DocumentRegistrationEntry $documentRegistrationEntry)
    {
        if (!Auth::user()->can('submit document for approval') || $documentRegistrationEntry->status->name !== 'Pending') {
            abort(403);
        }

        $request->validate([
            'document_file' => 'required|file|mimes:pdf,doc,docx,txt,xls,xlsx,csv|max:10240'
        ]);

        $documentFile = $request->file('document_file');

        DB::beginTransaction();
        try {
            $this->documentRegistryFileService->uploadFile($documentRegistrationEntry, $documentFile);
            DB::commit();


            return back()->with('success', 'File uploaded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('File upload failed: ' . $e->getMessage());
            return back()->with('error', 'Failed to upload file. Please try again.');
        }
    }

    public function download(Request $request, $id)
    {
        $file = DocumentRegistrationEntryFile::findOrFail($id);
        $entry = $file->registrationEntry;

        if (!auth()->user()->can('view all document registrations') &&
            (!auth()->user()->can('view own document registrations') || $entry->submitted_by !== auth()->id())) {
            abort(403, 'You do not have permission to download this file.');
        }

        return $this->documentRegistryFileService->download($entry, $id);
    }
}
