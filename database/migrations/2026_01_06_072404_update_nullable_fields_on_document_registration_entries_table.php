<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            $table->string('document_no')->nullable()->change();
            $table->string('revision_no')->nullable()->change();
            $table->string('device_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            $table->string('document_no')->nullable(false)->change();
            $table->string('revision_no')->nullable(false)->change();
            $table->string('device_name')->nullable(false)->change();
        });
    }
};

