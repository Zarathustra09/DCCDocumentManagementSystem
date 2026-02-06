<?php

namespace App\Services;

use App\Interfaces\DocumentRegistryFileServiceInterface;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\DocumentRegistrationEntryFileStatus;
use App\Models\DocumentRegistrationEntryStatus;
use App\Notifications\DocumentRegistryEntryStatusUpdated;
use App\Notifications\DocumentRegistryFileCreated;
use App\Notifications\DocumentRegistryFileStatusUpdated;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class DocumentFileService implements DocumentRegistryFileServiceInterface
{
    public function download(DocumentRegistrationEntry $entry, $fileId)
    {
        $file = $entry->files()->find($fileId);

        if (!$file || !Storage::disk('local')->exists($file->file_path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download(
            $file->file_path,
            $file->original_filename
        );
    }
    public function approve(DocumentRegistrationEntryFile $file)
    {
        $implementedStatus = DocumentRegistrationEntryFileStatus::where('name', 'Implemented')->first();
        $entryImplementedStatus = DocumentRegistrationEntryStatus::where('name', 'Implemented')->first();

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
            $user->notify(new DocumentRegistryEntryStatusUpdated($file->registrationEntry, $file->registrationEntry->status));
        }
    }

    public function reject(DocumentRegistrationEntryFile $file, string $reason)
    {
        $returnedStatus = DocumentRegistrationEntryFileStatus::where('name', 'Returned')->first();
        if (!$returnedStatus) {
            throw new \RuntimeException('File status "Returned" not found. Please contact administrator.');
        }

        $file->update([
            'status_id' => $returnedStatus->id,
            'implemented_by' => auth()->id(),
            'implemented_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $user = $file->registrationEntry->submittedBy;
        if ($user) {
            $user->notify(new DocumentRegistryFileStatusUpdated($file, $file->status->name));
        }
    }

    public function uploadFile(DocumentRegistrationEntry $entry, UploadedFile $documentFile)
    {
        $pendingStatus = DocumentRegistrationEntryFileStatus::where('name', 'Pending')->first();
        if (!$pendingStatus) {
            throw new \RuntimeException('File status "Pending" not found. Please contact administrator.');
        }

        $file = DocumentRegistrationEntryFile::create([
            'entry_id' => $entry->id,
            'file_path' => $documentFile->store('document_registrations', 'local'),
            'original_filename' => $documentFile->getClientOriginalName(),
            'mime_type' => $documentFile->getMimeType(),
            'file_size' => $documentFile->getSize(),
            'status_id' => $pendingStatus->id,
        ]);

        DocumentRegistryFileCreated::sendToAdmins($file);

        return $file;
    }
}
