<?php

namespace App\Services;

use App\Interfaces\DocumentRegistryServiceInterface;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\DocumentRegistrationEntryFileStatus;
use App\Models\DocumentRegistrationEntryStatus;
use App\Notifications\DocumentRegistryEntryCreated;
use App\Notifications\DocumentRegistryEntryStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DocumentRegistryService implements DocumentRegistryServiceInterface
{
    public function create(Request $request): DocumentRegistrationEntry
    {
        $rules = [
            'document_no' => 'nullable|string|max:100',
            'document_title' => 'required|string|max:255',
            'category_id' => 'required|exists:subcategories,id',
            'customer_id' => 'nullable|exists:customers,id',
            'revision_no' => 'nullable|string|max:50',
            'device_name' => 'nullable|string|max:255',
            'originator_name' => 'required|string|max:255',
            'remarks' => 'nullable|string',
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
                'remarks' => $request->remarks,
                'status_id' => $pendingStatus->id,
                'submitted_by' => Auth::id(),
                'submitted_at' => now(),
            ]);

            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $pendingFileStatus = DocumentRegistrationEntryFileStatus::where('name', 'Pending')->first();

                DocumentRegistrationEntryFile::create([
                    'entry_id' => $entry->id,
                    'file_path' => $file->store('document_registrations', 'local'),
                    'original_filename' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'status_id' => $pendingFileStatus->id,
                ]);
            }

            $entry->refresh();
            DB::commit();

            try {
                DocumentRegistryEntryCreated::sendToAdmins($entry);
            } catch (\Exception $e) {
                Log::error('Failed to send notification: ' . $e->getMessage());
            }

            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create document registration entry: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateMinimal(Request $request, DocumentRegistrationEntry $entry): DocumentRegistrationEntry
    {
        $request->validate([
            'category_id' => 'required|exists:subcategories,id',
            'customer_id' => 'required|exists:customers,id',
        ]);

        DB::beginTransaction();
        try {
            $entry->update([
                'category_id' => $request->category_id,
                'customer_id' => $request->customer_id,
            ]);

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Minimal update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateFull(Request $request, DocumentRegistrationEntry $entry): DocumentRegistrationEntry
    {
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
            $canEditDocNo = Auth::user()->can('edit document registration details');

            $allowedFields = [
                'document_title', 'category_id', 'customer_id', 'revision_no', 'device_name', 'document_no',
                'originator_name', 'remarks'
            ];

            if (! $canEditDocNo) {
                $allowedFields = array_diff($allowedFields, ['document_no']);
            }

            $entry->update($request->only($allowedFields));

            DB::commit();
            return $entry;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Full update failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function approve(Request $request, DocumentRegistrationEntry $entry): void
    {
        $implementedStatus = DocumentRegistrationEntryStatus::where('name', 'Implemented')->first();
        $implementedFileStatus = DocumentRegistrationEntryFileStatus::where('name', 'Implemented')->first();

        DB::beginTransaction();
        try {
            $entry->update([
                'status_id' => $implementedStatus->id,
                'implemented_by' => Auth::id(),
                'implemented_at' => now(),
                'rejection_reason' => null,
                'revision_notes' => null,
            ]);

            $entry->files()->update([
                'status_id' => $implementedFileStatus->id,
                'implemented_by' => Auth::id(),
                'implemented_at' => now(),
                'rejection_reason' => null,
            ]);

            $entry->refresh();
            DB::commit();

            $user = $entry->submittedBy;
            if ($user) {
                $user->notify(new DocumentRegistryEntryStatusUpdated($entry, $entry->status));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function reject(Request $request, DocumentRegistrationEntry $entry): void
    {
        $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $cancelledStatus = DocumentRegistrationEntryStatus::where('name', 'Cancelled')->first();
        $returnedFileStatus = DocumentRegistrationEntryFileStatus::where('name', 'Returned')->first();

        DB::beginTransaction();
        try {
            $entry->update([
                'status_id' => $cancelledStatus->id,
                'implemented_by' => Auth::id(),
                'implemented_at' => now(),
                'rejection_reason' => $request->rejection_reason,
                'revision_notes' => null,
            ]);

            $entry->files()->update([
                'status_id' => $returnedFileStatus->id,
                'implemented_by' => Auth::id(),
                'implemented_at' => now(),
                'rejection_reason' => $request->rejection_reason,
            ]);

            $entry->refresh();
            DB::commit();

            $user = $entry->submittedBy;
            if ($user) {
                $user->notify(new DocumentRegistryEntryStatusUpdated($entry, $entry->status));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function requireRevision(Request $request, DocumentRegistrationEntry $entry): void
    {
        $request->validate([
            'revision_notes' => 'required|string'
        ]);

        $cancelledStatus = DocumentRegistrationEntryStatus::where('name', 'Cancelled')->first();
        $cancelledFileStatus = DocumentRegistrationEntryFileStatus::where('name', 'Cancelled')->first();

        DB::beginTransaction();
        try {
            $entry->update([
                'status_id' => $cancelledStatus->id,
                'implemented_by' => Auth::id(),
                'implemented_at' => now(),
                'revision_notes' => $request->revision_notes,
                'rejection_reason' => 'Revision required. Please see revision notes.',
            ]);

            $entry->files()->update([
                'status_id' => $cancelledFileStatus->id,
                'implemented_by' => Auth::id(),
                'implemented_at' => now(),
                'rejection_reason' => 'Revision required. Please see revision notes.',
            ]);

            $entry->refresh();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Require revision failed: ' . $e->getMessage());
            throw $e;
        }
    }

    public function withdraw(DocumentRegistrationEntry $entry): void
    {
        DB::beginTransaction();
        try {
            $entry->files()->delete();
            $entry->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdraw failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
