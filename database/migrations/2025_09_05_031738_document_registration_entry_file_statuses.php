<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Create the file status table
        Schema::create('document_registration_entry_file_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default statuses
        DB::table('document_registration_entry_file_statuses')->insert([
            ['name' => 'Pending', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Implemented', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Returned', 'created_at' => now(), 'updated_at' => now()]
        ]);

        // Add status_id column to document_registration_entry_files
        Schema::table('document_registration_entry_files', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable()->after('status')->constrained('document_registration_entry_file_statuses');
        });

        // Migrate existing enum data to foreign key references
        DB::statement("
            UPDATE document_registration_entry_files SET status_id = CASE
                WHEN status = 'pending' THEN 1
                WHEN status = 'approved' THEN 2
                WHEN status = 'rejected' THEN 3
                ELSE 1
            END
        ");

        // Make status_id not nullable and set default
        Schema::table('document_registration_entry_files', function (Blueprint $table) {
            $table->foreignId('status_id')->nullable(false)->default(1)->change();
        });

        // Drop the old enum column
        Schema::table('document_registration_entry_files', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        // Add back the enum column
        Schema::table('document_registration_entry_files', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('file_size');
        });

        // Migrate data back from foreign key to enum
        DB::statement("
            UPDATE document_registration_entry_files
            SET status = CASE
                WHEN status_id = 1 THEN 'pending'
                WHEN status_id = 2 THEN 'approved'
                WHEN status_id = 3 THEN 'rejected'
                ELSE 'pending'
            END
        ");

        // Drop the foreign key and status_id column
        Schema::table('document_registration_entry_files', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        // Drop the status table
        Schema::dropIfExists('document_registration_entry_file_statuses');
    }
};
