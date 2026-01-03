<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Throwable;

class SyncEmployeesToUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $sourceDb;
    public string $targetDb;
    public string $lockKey;

    public function __construct(string $sourceDb, string $targetDb, string $lockKey)
    {
        $this->sourceDb = $sourceDb;
        $this->targetDb = $targetDb;
        $this->lockKey = $lockKey;
    }

    public function handle(): void
    {
        $sourceTable = "{$this->sourceDb}.employees";
        $targetTable = "{$this->targetDb}.users";

        Log::info("SyncEmployeesToUsersJob started: {$sourceTable} -> {$targetTable}");

        try {
            DB::table($sourceTable)->orderBy('id')->chunk(200, function ($employees) use ($targetTable) {
                foreach ($employees as $e) {
                    // authoritative key
                    $empNo = trim((string) ($e->employee_no ?? ''));

                    if ($empNo === '' || $empNo === '0') {
                        // skip invalid/missing employee_no to avoid cross-assignment
                        continue;
                    }

                    $normalizeDate = function ($d) {
                        return ($d && $d !== '0000-00-00') ? $d : null;
                    };

                    $data = [
                        'employee_no'    => $empNo,
                        'username'       => $e->username ?: null,
                        'password'       => $e->password ?: null,
                        'firstname'      => $e->firstname ?: null,
                        'middlename'     => $e->middlename ?: null,
                        'lastname'       => $e->lastname ?: null,
                        'address'        => $e->address ?: null,
                        'birthdate'      => $normalizeDate($e->birthdate),
                        'contact_info'   => $e->contact_info ?: null,
                        'gender'         => $e->gender ?: null,
                        'datehired'      => $normalizeDate($e->datehired),
                        'profile_image'  => $e->photo ?: null,
                        'created_on'     => $normalizeDate($e->created_on),
                        'barcode'        => $e->barcode ?: null,
                        'email'          => $e->email ?: null,
                        'separationdate' => $normalizeDate($e->separationdate),
                    ];

                    try {
                        DB::transaction(function () use ($targetTable, $empNo, $data) {
                            // lock existing row for update to avoid race conditions
                            $existing = DB::table($targetTable)
                                ->where('employee_no', $empNo)
                                ->lockForUpdate()
                                ->first();

                            if ($existing) {
                                DB::table($targetTable)
                                    ->where('employee_no', $empNo)
                                    ->update(array_merge($data, ['updated_at' => now()]));
                            } else {
                                DB::table($targetTable)
                                    ->insert(array_merge($data, ['created_at' => now(), 'updated_at' => now()]));
                            }
                        });
                    } catch (Throwable $ex) {
                        // log and continue with other rows
                        Log::error("SyncEmployeesToUsersJob: failed to upsert employee_no={$empNo}: " . $ex->getMessage());
                    }
                }
            });
        } catch (Throwable $ex) {
            Log::error('SyncEmployeesToUsersJob failed: ' . $ex->getMessage());
        } finally {
            // ensure lock released
            Cache::forget($this->lockKey);
            Log::info('SyncEmployeesToUsersJob finished and lock released.');
        }
    }

    public function failed(Throwable $exception): void
    {
        // ensure lock released on failure
        Cache::forget($this->lockKey);
        Log::error('SyncEmployeesToUsersJob failed with exception: ' . $exception->getMessage());
    }
}
