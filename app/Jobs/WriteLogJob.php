<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WriteLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $message;
    public string $channel;

    /**
     * Create a new job instance.
     */
    public function __construct(string $message, string $channel = 'queue_processing')
    {
        $this->message = $message;
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Write a log entry to the specified channel so ProcessQueue testing can be observed
        Log::channel($this->channel)->info($this->message);

        // light sleep to simulate work (optional)
        usleep(100000);
    }
}
