<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Jobs\WriteLogJob;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Dispatch 10 test jobs to the default queue. Adjust $count as needed.
        $count = 10;

        for ($i = 1; $i <= $count; $i++) {
            WriteLogJob::dispatch("Test queued job #{$i} dispatched by JobSeeder", 'queue_processing')
                ->onQueue('default');
        }

        if ($this->command) {
            $this->command->info("Dispatched {$count} WriteLogJob jobs to the 'default' queue.");
        }
    }
}
