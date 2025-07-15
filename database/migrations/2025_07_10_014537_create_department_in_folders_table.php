<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->enum('department', [
                'IT', 'Finance', 'QA', 'HR', 'Purchasing',
                'Sales', 'Operations', 'General',
                'Business Unit 1', 'Business Unit 2', 'Business Unit 3'
            ])->default('General')->after('name');
        });
    }

    public function down()
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }
};
