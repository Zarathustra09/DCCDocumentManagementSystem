<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            $table->string('control_no')->nullable()->after('rejection_reason');
            $table->string('dcn_no')->nullable()->after('control_no');
        });

        DB::table('document_registration_entries')
            ->whereNull('control_no')
            ->get()
            ->each(function ($record) {
                DB::table('document_registration_entries')
                    ->where('id', $record->id)
                    ->update([
                        'control_no' => 'DCC-' . Str::random(9)
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            $table->dropColumn('control_no');
            $table->dropColumn('dcn_no');
        });
    }
};
