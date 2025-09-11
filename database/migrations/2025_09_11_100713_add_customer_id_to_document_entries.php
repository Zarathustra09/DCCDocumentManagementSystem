<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            // Add the new customer_id column
            $table->foreignId('customer_id')->nullable()->after('customer')->constrained('customers')->onDelete('set null');
        });

        // Here you could add code to migrate data from customer string to customer_id
        // Then drop the old customer column
        Schema::table('document_registration_entries', function (Blueprint $table) {
            $table->dropColumn('customer');
        });
    }

    public function down(): void
    {
        Schema::table('document_registration_entries', function (Blueprint $table) {
            // Re-add the customer string column
            $table->string('customer')->after('originator_name');
            // Drop the foreign key and customer_id column
            $table->dropForeign(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
