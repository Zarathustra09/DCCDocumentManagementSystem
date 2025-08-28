<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class RunSqlSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('backup/insert.sql');
        if (File::exists($path)) {
            $sql = File::get($path);

            // Remove comments
            $sql = preg_replace('/--.*\n/', '', $sql);
            $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);

            // Split statements by semicolon
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            foreach ($statements as $statement) {
                if (!empty($statement)) {
                    DB::unprepared($statement . ';');
                }
            }
        } else {
            $this->command->error("SQL file not found: $path");
        }
    }
}
