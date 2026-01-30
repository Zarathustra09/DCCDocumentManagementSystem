<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            $table->timestamp('expiration_date')->nullable()->after('implemented_at');
        });

        // Update existing records that have implemented_at set
        DB::table('document_registration_entries')
            ->whereNotNull('implemented_at')
            ->chunkById(100, function ($entries) {
                foreach ($entries as $entry) {
                    $implementedAt = Carbon::parse($entry->implemented_at);
                    $expirationDate = $implementedAt->copy()->addMonth();

                    DB::table('document_registration_entries')
                        ->where('id', $entry->id)
                        ->update([
                            'expiration_date' => $expirationDate
                        ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            $table->dropColumn('expiration_date');
        });
    }
};
