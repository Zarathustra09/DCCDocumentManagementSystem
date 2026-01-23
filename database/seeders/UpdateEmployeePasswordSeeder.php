<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class UpdateEmployeePasswordSeeder extends Seeder
{
    public function run(): void
    {
        // ...existing code...
        $employeeNo  = 337;
        $plainSecret = 'SPIRhenov030201';

        $user = User::where('employee_no', $employeeNo)->first();

        if (! $user) {
            $this->command->error("Employee {$employeeNo} not found.");
            Log::warning("Password update skipped: employee_no {$employeeNo} missing.");
            return;
        }

        $user->password = password_hash($plainSecret, PASSWORD_BCRYPT);
        $user->save();

        $this->command->info("Password updated for employee_no {$employeeNo}.");
        Log::info("Password updated through seeder for employee_no {$employeeNo}.");
        // ...existing code...
    }
}

