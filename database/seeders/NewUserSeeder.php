<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ensure all NOT NULL columns from the CREATE TABLE are provided
        $employeeNo = '999';
        $nowDate = now()->toDateString();

        // write raw DB values so the stored password is exactly the output of password_hash(...)
        DB::table('users')->updateOrInsert(
            ['employee_no' => $employeeNo],
            [
                'employee_no'    => $employeeNo,
                'username'       => 'basicuser',
                'password'       => password_hash('BasicPass123!', PASSWORD_BCRYPT), // native PHP hashing
                'firstname'      => 'Basic',
                'middlename'     => 'Access',
                'lastname'       => 'Credential',
                'address'        => '123 Default St, Sample City',
                'birthdate'      => '1990-01-01',                      // NOT NULL per CREATE TABLE
                'contact_info'   => '000-000-0000',
                'gender'         => 'N/A',
                'datehired'      => null,
                'profile_image'  => 'profiles/basicuser.png',          // NOT NULL
                'created_on'     => $nowDate,                          // NOT NULL
                'barcode'        => 'BASIC-999',                       // NOT NULL
                'email'          => 'basic.user@example.com',
                'separationdate' => null,
                'department_id'  => null,
                'organization_id'=> null,
                'created_at'     => now(),
                'updated_at'     => now(),
            ]
        );

        // get the Eloquent model and assign role via model (so Spatie works)
        $basicUser = User::where('employee_no', $employeeNo)->first();

        if ($basicUser && ! $basicUser->hasRole('BasicRole')) {
            $basicUser->assignRole('BasicRole');
        }
    }
}
