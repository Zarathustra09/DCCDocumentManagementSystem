<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('birthdate')->nullable()->change();
            $table->dateTime('datehired')->nullable()->change();
            $table->dateTime('created_on')->nullable()->change();
            $table->dateTime('separationdate')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birthdate')->nullable()->change();
            $table->date('datehired')->nullable()->change();
            $table->date('created_on')->nullable()->change();
            $table->date('separationdate')->nullable()->change();
        });
    }
};
