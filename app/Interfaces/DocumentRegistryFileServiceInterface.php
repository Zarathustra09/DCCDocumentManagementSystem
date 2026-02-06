<?php

namespace App\Interfaces;

use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use Illuminate\Http\UploadedFile;

interface DocumentRegistryFileServiceInterface
{
    public function download(DocumentRegistrationEntry $entry, $fileId);
//    public function preview(DocumentRegistrationEntry $entry, $fileId);
//    public function previewApi(DocumentRegistrationEntry $entry, $fileId);
    public function approve(DocumentRegistrationEntryFile $file);
    public function reject(DocumentRegistrationEntryFile $file, string $reason);
    public function uploadFile(DocumentRegistrationEntry $entry, UploadedFile $file);
}
