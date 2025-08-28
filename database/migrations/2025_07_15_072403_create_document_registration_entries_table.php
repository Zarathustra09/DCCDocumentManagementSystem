<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_registration_entries', function (Blueprint $table) {
            $table->id();
            $table->string('document_title');
            $table->string('document_no');
            $table->string('revision_no');
            $table->string('device_name');
            $table->string('originator_name');
            $table->string('customer');
            $table->text('remarks')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('implemented_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('implemented_at')->nullable();
//            $table->text('rejection_reason')->nullable();
//            $table->text('revision_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_registration_entries');
    }
};
