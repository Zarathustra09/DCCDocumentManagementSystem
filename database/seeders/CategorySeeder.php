<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'SPI In-House Specification', 'code' => 'CN2', 'is_active' => true],
            ['name' => 'Probe Card', 'code' => 'CN2', 'is_active' => true],
            ['name' => 'PCB Assembly', 'code' => 'PA2', 'is_active' => true],
            ['name' => 'Mechatronics and Automation', 'code' => 'MA', 'is_active' => true],
            ['name' => 'Mechatronics and Automation Electrical', 'code' => 'MAE', 'is_active' => true],
            ['name' => 'Mechatronics and Automation PCB', 'code' => 'MAP', 'is_active' => true],
            ['name' => 'Mechatronics and Automation Mechanical', 'code' => 'MAM', 'is_active' => true],
            ['name' => 'Mechatronics and Automation Coding/Program', 'code' => 'MAC', 'is_active' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
