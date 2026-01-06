<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('last_control_numbers', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('last_number')->default(0);
            $table->timestamps();
        });

        DB::table('last_control_numbers')->insert([
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('last_control_numbers');
    }
};
