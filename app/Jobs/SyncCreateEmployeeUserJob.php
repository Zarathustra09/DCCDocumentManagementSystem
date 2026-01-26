<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class SyncCreateEmployeeUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $targetConnection;
    public array $payload;

    public function __construct(string $targetConnection, array $payload)
    {
        $this->targetConnection = $targetConnection;
        $this->payload = $payload;
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

        $payload['password'] = ($payload['password'] ?? '') !== ''
            ? $payload['password']
            : password_hash($plainSecret, PASSWORD_BCRYPT);

        $rawBarcode = trim((string) ($payload['barcode'] ?? ''));
        $payload['barcode'] = ($rawBarcode === '' || $rawBarcode === '?') ? 'N/A' : $rawBarcode;

        foreach (['birthdate', 'datehired', 'created_on', 'separationdate'] as $dateField) {
            $payload[$dateField] = $this->parseDateTime($payload[$dateField] ?? null);
        }

        $conn = DB::connection($this->targetConnection);
        $now = Carbon::now()->format('Y-m-d H:i:s');

        try {
            $conn->table('users')->insert([
                'employee_no'     => $empNo,
                'username'        => $payload['username'] ?? $empNo,
                'password'        => $payload['password'],
                'firstname'       => $payload['firstname'] ?? '',
                'middlename'      => $payload['middlename'] ?? '',
                'lastname'        => $payload['lastname'] ?? '',
                'address'         => $payload['address'] ?? '',
                'birthdate'       => $payload['birthdate'],
                'contact_info'    => $payload['contact_info'] ?? '',
                'gender'          => $payload['gender'] ?? '',
                'datehired'       => $payload['datehired'],
                'profile_image'   => $payload['photo'] ?? $payload['profile_image'] ?? 'N/A',
                'created_on'      => $payload['created_on'],
                'barcode'         => $payload['barcode'],
                'email'           => $payload['email'] ?? null,
                'separationdate'  => $payload['separationdate'],
                'department_id'   => $payload['department_id'] ?? null,
                'organization_id' => $payload['organization_id'] ?? null,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            Log::channel('spears24sync')->info("Created new user for employee_no={$empNo}");
        } catch (Throwable $e) {
            Log::channel('spears24sync')->error("Failed to create user for employee_no={$empNo}: " . $e->getMessage());
            Log::channel('spears24sync')->debug("Payload for employee_no={$empNo}: " . json_encode($payload));
            throw $e;
        }
    }

    public function failed(Throwable $exception): void
    {
        $empNo = $this->payload['employee_no'] ?? 'unknown';
        Log::channel('spears24sync')->error("SyncCreateEmployeeUserJob failed for employee_no={$empNo}: " . $exception->getMessage());
    }
}
