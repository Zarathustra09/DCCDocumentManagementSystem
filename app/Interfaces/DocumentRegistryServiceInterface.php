<?php

namespace App\Interfaces;
use Illuminate\Http\Request;
use App\Models\DocumentRegistrationEntry;

interface DocumentRegistryServiceInterface
{
    public function create(Request $request): DocumentRegistrationEntry;
    public function updateMinimal(Request $request, DocumentRegistrationEntry $entry): DocumentRegistrationEntry;
    public function updateFull(Request $request, DocumentRegistrationEntry $entry): DocumentRegistrationEntry;
    public function approve(Request $request, DocumentRegistrationEntry $entry): void;
    public function reject(Request $request, DocumentRegistrationEntry $entry): void;
    public function requireRevision(Request $request, DocumentRegistrationEntry $entry): void;
    public function withdraw(DocumentRegistrationEntry $entry): void;
}
