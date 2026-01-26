<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class SyncSingleEmployeeToUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $sourceConnection;
    public string $targetConnection;
    public array $payload;

    public function __construct(string $sourceConnection, string $targetConnection, array $payload)
    {
        $this->sourceConnection = $sourceConnection;
        $this->targetConnection = $targetConnection;
        $this->payload = $payload;
    }

    private function parseDate($value): string
    {
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return Carbon::now()->format('Y-m-d');
        }
    }

    private function parseDateTime($value): ?string
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

    public function handle(): void
    {
        $payload = $this->payload;
        $empNo = $payload['employee_no'];
        $plainSecret = $payload['__plainSecret'] ?? 'welcome123#';

        $rawPassword = $payload['password'] ?? null;
        if ($rawPassword === null || $rawPassword === '') {
            $payload['password'] = password_hash($plainSecret, PASSWORD_BCRYPT);
        }

        $rawBarcode = trim((string) ($payload['barcode'] ?? ''));
        if ($rawBarcode === '' || $rawBarcode === '?') {
            $payload['barcode'] = 'N/A';
        }

        foreach (['birthdate', 'datehired', 'created_on', 'separationdate'] as $dateField) {
            $payload[$dateField] = $this->parseDateTime($payload[$dateField] ?? null);
        }

        $conn = DB::connection($this->targetConnection);
        $existing = $conn->selectOne('SELECT id FROM users WHERE employee_no = ?', [$empNo]);
        if (! $existing) {
            Log::channel('spears24sync')->info("Skipping employee_no={$empNo} (user not found; creation handled elsewhere)");
            return;
        }

        try {
            $conn->update(
                'UPDATE users SET username = ?, password = ?, barcode = ?, email = ?, created_on = ?, separationdate = ? WHERE id = ?',
                [
                    $payload['username'] ?? null,
                    $payload['password'],
                    $payload['barcode'],
                    $payload['email'] ?? null,
                    $payload['created_on'],
                    $payload['separationdate'],
                    $existing->id,
                ]
            );
            Log::channel('spears24sync')->info("Successfully synced employee_no={$empNo}");
        } catch (Throwable $e) {
            Log::channel('spears24sync')->error("Failed to save employee_no={$empNo}: " . $e->getMessage());
            Log::channel('spears24sync')->debug("Payload for employee_no={$empNo}: " . json_encode($payload));
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $empNo = $this->payload['employee_no'] ?? 'unknown';
        Log::channel('spears24sync')->error("SyncSingleEmployeeToUserJob failed for employee_no={$empNo}: " . $exception->getMessage());
    }
}
