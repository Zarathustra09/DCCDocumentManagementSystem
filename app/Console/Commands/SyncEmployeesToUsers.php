<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Jobs\SyncEmployeesToUsersJob;

class SyncEmployeesToUsers extends Command
{
    protected $signature = 'employees:sync-users';
    protected $description = 'Dispatch a queued job to sync data from employees table into users (upsert by employee_no)';

    public function handle(): int
    {
        $sourceDb = env('SPEARS_DB', config('database.connections.mysql.database'));
        if (! $sourceDb) {
            $this->error('SPEARS_DB is not set.');
            return 1;
        }

        $targetDb = config('database.connections.mysql.database');

        $lockKey = 'employees_sync_lock';

        // Prevent duplicate dispatches (keeps command fast)
        if (! Cache::add($lockKey, true, 3600)) {
            $this->info('Sync already scheduled or running.');
            return 0;
        }

        // Dispatch queued job (job will release the lock)
        SyncEmployeesToUsersJob::dispatch($sourceDb, $targetDb, $lockKey)->onQueue('default');

        $this->info("Sync job dispatched for source: {$sourceDb}.employees -> target: {$targetDb}.users");
        return 0;
    }
}
