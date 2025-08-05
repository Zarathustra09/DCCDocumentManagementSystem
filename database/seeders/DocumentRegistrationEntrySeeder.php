<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRegistrationEntry;
use App\Models\DocumentRegistrationEntryFile;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentRegistrationEntrySeeder extends Seeder
{
    public function run()
    {
        $users = User::pluck('id')->toArray();
        $statuses = array_keys(DocumentRegistrationEntry::STATUSES);

        for ($i = 1; $i <= 50; $i++) {
            $status = $statuses[array_rand($statuses)];
            $submittedBy = $users[array_rand($users)];
            $approvedBy = $users[array_rand($users)];

            $entry = DocumentRegistrationEntry::create([
                'document_title' => 'Document Title ' . $i,
                'document_no' => 'DOC-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'revision_no' => rand(0, 5),
                'device_name' => 'Device ' . rand(1, 10),
                'originator_name' => 'Originator ' . rand(1, 10),
                'customer' => 'Customer ' . rand(1, 5),
                'remarks' => Str::random(20),
                'status' => $status,
                'submitted_by' => $submittedBy,
                'approved_by' => $status === 'approved' ? $approvedBy : null,
                'submitted_at' => Carbon::now()->subDays(rand(1, 100)),
                'approved_at' => $status === 'approved' ? Carbon::now()->subDays(rand(1, 99)) : null,
            ]);

            $fileCount = rand(1, 3);
            for ($j = 1; $j <= $fileCount; $j++) {
                DocumentRegistrationEntryFile::create([
                    'entry_id' => $entry->id,
                    'file_path' => 'documents/' . Str::random(10) . '.pdf',
                    'original_filename' => 'File_' . $i . '_' . $j . '.pdf',
                    'mime_type' => 'application/pdf',
                    'file_size' => rand(10000, 500000),
                    'status' => $status,
                    'rejection_reason' => $status === 'rejected' ? 'Random rejection reason' : null,
                    'approved_at' => $status === 'approved' ? Carbon::now()->subDays(rand(1, 99)) : null,
                    'approved_by' => $status === 'approved' ? $approvedBy : null,
                ]);
            }
        }
    }
}
