<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Log;

class ProcessQueue extends Command
{
    protected $signature = 'app:process-queue';
    protected $description = 'Process queued jobs only when jobs are available';

    public function handle()
    {
        $queueSize = Queue::size();

        if ($queueSize === 0) {
            Log::channel('queue_processing')
                ->info('Queue empty. Skipping worker at ' . now());
            return Command::SUCCESS;
        }

        Log::channel('queue_processing')
            ->info("Queue worker starting ({$queueSize} jobs) at " . now());

        $this->call('queue:work', [
            '--stop-when-empty' => true,
            '--max-time'        => 60,
            '--timeout'        => 60,
            '--tries'          => 3,
        ]);

        Log::channel('queue_processing')
            ->info('Queue worker finished at ' . now());

        return Command::SUCCESS;
    }
}
