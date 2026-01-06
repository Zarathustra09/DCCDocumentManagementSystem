<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AssignBasicRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeNumbers = ['392', '393', '394', '395', '396', '397', '398', '399', '400', '401'];

        foreach ($employeeNumbers as $employeeNo) {
            $user = User::where('employee_no', $employeeNo)->first();

            if ($user && !$user->hasRole('BasicRole')) {
                $user->assignRole('BasicRole');
            }
        }
    }
}
