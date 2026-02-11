<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop old column/index if they exist to avoid duplicate '' errors
        if (Schema::hasColumn('exports', 'control_no')) {
            Schema::table('exports', function (Blueprint $table) {
                // drop index if exists
                $hasIndex = collect(DB::select("SHOW INDEX FROM `exports` WHERE Key_name = 'exports_control_no_unique'"))->isNotEmpty();
                if ($hasIndex) {
                    $table->dropUnique('exports_control_no_unique');
                }
            });

            Schema::table('exports', function (Blueprint $table) {
                $table->dropColumn('control_no');
            });
        }

        // Re-add column nullable first
        Schema::table('exports', function (Blueprint $table) {
            $table->string('control_no')->nullable()->after('employee_no');
        });

        // Backfill unique control_no values for all rows
        DB::table('exports')
            ->orderBy('id')
            ->chunkById(500, function ($exports) {
                foreach ($exports as $export) {
                    do {
                        $controlNo = 'EXP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
                    } while (DB::table('exports')->where('control_no', $controlNo)->exists());

                    DB::table('exports')
                        ->where('id', $export->id)
                        ->update(['control_no' => $controlNo]);
                }
            });

        // Enforce not null + unique index
        Schema::table('exports', function (Blueprint $table) {
            $table->string('control_no')->nullable(false)->change();
            $table->unique('control_no', 'exports_control_no_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exports', function (Blueprint $table) {
            $table->dropUnique('exports_control_no_unique');
            $table->dropColumn('control_no');
        });
    }
};
