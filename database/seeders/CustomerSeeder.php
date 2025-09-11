<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            ['name' => 'Apple Inc.', 'code' => 'APP', 'is_active' => true],
            ['name' => 'Microsoft Corporation', 'code' => 'MIC', 'is_active' => true],
            ['name' => 'Google LLC', 'code' => 'GOO', 'is_active' => true],
            ['name' => 'Amazon Web Services', 'code' => 'AMA', 'is_active' => true],
            ['name' => 'Tesla Inc.', 'code' => 'TES', 'is_active' => true],
            ['name' => 'Boeing Company', 'code' => 'BOE', 'is_active' => true],
            ['name' => 'General Electric', 'code' => 'GEN', 'is_active' => true],
            ['name' => 'Ford Motor Company', 'code' => 'FOR', 'is_active' => true],
            ['name' => 'Samsung Electronics', 'code' => 'SAM', 'is_active' => true],
            ['name' => 'Intel Corporation', 'code' => 'INT', 'is_active' => true],
            ['name' => 'Nvidia Corporation', 'code' => 'NVI', 'is_active' => true],
            ['name' => 'Sony Corporation', 'code' => 'SON', 'is_active' => true],
            ['name' => 'Toyota Motor Corporation', 'code' => 'TOY', 'is_active' => true],
            ['name' => 'Siemens AG', 'code' => 'SIE', 'is_active' => true],
            ['name' => 'Honeywell International', 'code' => 'HON', 'is_active' => true],
            ['name' => 'Lockheed Martin', 'code' => 'LOC', 'is_active' => true],
            ['name' => 'Caterpillar Inc.', 'code' => 'CAT', 'is_active' => true],
            ['name' => 'Johnson & Johnson', 'code' => 'JOH', 'is_active' => true],
            ['name' => 'Pfizer Inc.', 'code' => 'PFI', 'is_active' => true],
            ['name' => 'Coca-Cola Company', 'code' => 'COC', 'is_active' => true],
            ['name' => 'Walmart Inc.', 'code' => 'WAL', 'is_active' => false],
            ['name' => 'Facebook Meta', 'code' => 'FAC', 'is_active' => false],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
