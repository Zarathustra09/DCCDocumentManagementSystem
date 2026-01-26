<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Jobs\SyncSingleEmployeeToUserJob;
use Carbon\Carbon;

class SyncEmployeesToUsers extends Command
{
    protected $signature = 'employees:sync-users {--source= : source DB connection name} {--target= : target DB connection name}';
    protected $description = 'Dispatch a queued job to sync employees into users (employee_no ascending, compare before update)';

    private function normalizeDateTime($value): ?string
    {
        $value = is_null($value) ? null : trim((string) $value);
        if ($value === '' || $value === '0000-00-00' || $value === '0000-00-00 00:00:00') {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function handle(): int
    {
        // default source connection name -> db_spears if present, otherwise use env or fallback
        $sourceConn = $this->option('source') ?: (array_key_exists('db_spears', config('database.connections')) ? 'db_spears' : config('database.default'));
        if (! $sourceConn) {
            $this->error('No source DB connection configured.');
            Log::channel('spears24sync')->error('No source DB connection configured.');
            return 1;
        }

        // default target connection name -> app default connection
        $targetConn = $this->option('target') ?: config('database.default');
        if (! $targetConn) {
            $this->error('No target DB connection configured.');
            Log::channel('spears24sync')->error('No target DB connection configured.');
            return 1;
        }

        $lockKey = "employees_sync_lock:{$sourceConn}:{$targetConn}";

        // Prevent duplicate dispatches (keeps command fast)
        if (! Cache::add($lockKey, true, 3600)) {
            $this->info('Sync already scheduled or running.');
            Log::channel('spears24sync')->info('Sync already scheduled or running, skipping dispatch.');
            return 0;
        }

        DB::connection($sourceConn)
            ->table('employees')
            ->orderBy('employee_no')
            ->chunk(200, function ($employees) use ($sourceConn, $targetConn) {
                foreach ($employees as $e) {
                    $empNo = trim((string) ($e->employee_no ?? ''));
                    if ($empNo === '' || $empNo === '0') {
                        continue;
                    }
                    $payload = (array) $e;
                    $payload['employee_no'] = $empNo;
                    $payload['__plainSecret'] = 'welcome123#';

                    foreach (['birthdate', 'datehired', 'created_on', 'separationdate'] as $dateField) {
                        $payload[$dateField] = $this->normalizeDateTime($payload[$dateField] ?? null);
                    }

                    SyncSingleEmployeeToUserJob::dispatch($sourceConn, $targetConn, $payload)->onQueue('default');
                    Log::channel('spears24sync')->info("Queued sync job for employee_no={$empNo}");
                }
            });

        Cache::forget($lockKey);

        $msg = "Sync job dispatched for source connection: {$sourceConn}.employees -> target connection: {$targetConn}.users";
        $this->info($msg);
        Log::channel('spears24sync')->info($msg);
        Log::channel('spears24sync')->info('All sync jobs have been queued successfully.');

        return 0;
    }
}
