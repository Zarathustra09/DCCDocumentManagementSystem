<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'employee_no'     => $this->faker->unique()->numerify('EMP#####'),
            'username'        => $this->faker->unique()->userName,
            'password'        => Hash::make('password'),
            'firstname'       => $this->faker->firstName,
            'middlename'      => $this->faker->firstName,
            'lastname'        => $this->faker->lastName,
            'address'         => $this->faker->address,
            'birthdate'       => $this->faker->date(),
            'contact_info'    => $this->faker->phoneNumber,
            'gender'          => $this->faker->randomElement(['male', 'female']),
            'datehired'       => $this->faker->date(),
            'profile_image'   => '',
            'created_on'      => now(),
            'barcode'         => Str::random(10),
            'email'           => $this->faker->unique()->safeEmail,
            'separationdate'  => null,
            'email_verified_at' => now(),
            'remember_token'  => Str::random(10),
        ];
    }
}
