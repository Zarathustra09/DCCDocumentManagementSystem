<?php

namespace App\Interfaces;

use App\Models\DocumentRegistrationEntry;

interface DocumentRegistryFileServiceInterface
{
    public function download(DocumentRegistrationEntry $entry, $fileId);
    public function preview(DocumentRegistrationEntry $entry, $fileId);
    public function previewApi(DocumentRegistrationEntry $entry, $fileId);
}
