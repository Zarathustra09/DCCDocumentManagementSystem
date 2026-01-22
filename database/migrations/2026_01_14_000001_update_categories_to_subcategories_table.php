<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('categories', 'subcategories');

        Schema::table('subcategories', function (Blueprint $table) {
            $table->foreignId('main_category_id')
                ->nullable()
                ->constrained('main_categories')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('subcategories', function (Blueprint $table) {
            $table->dropForeign(['main_category_id']);
            $table->dropColumn('main_category_id');
        });

        Schema::rename('subcategories', 'categories');
    }
};

