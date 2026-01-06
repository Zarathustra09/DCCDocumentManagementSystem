<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddSingleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeData = [
            'employee_no' => '391',
            'username' => 'monica',
            'password' => '$2y$10$cO7g.0Wg6eBIgwfIIu6N5OvCR7oRDXi/4dNxZKcYtH8Kf5zm3dhMi',
            'firstname' => 'Niña Monica',
            'middlename' => 'Barte',
            'lastname' => 'Sta. Ana',
            'address' => 'Sitio Looban Banaba Kanluran, Batangas City',
            'birthdate' => '2002-09-22',
            'contact_info' => '0976-509-0563',
            'gender' => 'Female',
            'datehired' => '2025-08-01',
            'profile_image' => 'Untitled design (9).png',
            'created_on' => '2025-08-06',
            'barcode' => '391',
            'email' => null,
            'separationdate' => null,
            'department_id' => 12,
            'organization_id' => 2,
            'updated_at' => now(),
        ];

        DB::table('users')->updateOrInsert(
            ['employee_no' => $employeeData['employee_no']],
            $employeeData
        );

        $user = User::where('employee_no', '391')->first();

        if ($user && !$user->hasRole('BasicRole')) {
            $user->assignRole('BasicRole');
        }
    }
}
