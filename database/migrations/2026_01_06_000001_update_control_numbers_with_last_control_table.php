<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $tracker = DB::table('last_control_numbers')->lockForUpdate()->first();

            if (!$tracker) {
                $trackerId = DB::table('last_control_numbers')->insertGetId([
                    'last_number' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $tracker = DB::table('last_control_numbers')->where('id', $trackerId)->lockForUpdate()->first();
            }

            $lastNumber = $tracker->last_number;
            $currentYear = now()->format('y');

            $entries = DB::table('document_registration_entries')
                ->select('id')
                ->orderBy('created_at')
                ->lockForUpdate()
                ->get();

            foreach ($entries as $entry) {
                $lastNumber++;
                DB::table('document_registration_entries')
                    ->where('id', $entry->id)
                    ->update([
                        'control_no' => sprintf('%s-%04d', $currentYear, $lastNumber),
                    ]);
            }

            DB::table('last_control_numbers')
                ->where('id', $tracker->id)
                ->update([
                    'last_number' => $lastNumber,
                    'updated_at' => now(),
                ]);
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            DB::table('document_registration_entries')->update(['control_no' => null]);
            DB::table('last_control_numbers')->update([
                'last_number' => 0,
                'updated_at' => now(),
            ]);
        });
    }
};
