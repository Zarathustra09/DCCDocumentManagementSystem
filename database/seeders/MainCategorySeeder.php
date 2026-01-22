<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\MainCategory;
use App\Models\SubCategory;

class MainCategorySeeder extends Seeder
{
    protected function normalize(string $s): string
    {
        return preg_replace('/[^a-z0-9]/', '', strtolower(trim($s)));
    }

    public function run()
    {
        DB::transaction(function () {
            // Create main categories (use the exact names provided)
            $mains = [
                'a' => MainCategory::firstOrCreate(['name' => 'General Categories']),
                'b' => MainCategory::firstOrCreate(['name' => 'Mechatronics and Automation']),
                'c' => MainCategory::firstOrCreate(['name' => 'SPI In-house Specfication']),
            ];

            // Mapping of existing subcategory names to main category keys (a/b/c)
            $map = [
                'SPI In-House Specification' => 'c',
                'Probe Card' => 'a',
                'PCB Assembly' => 'a',
                'Mechatronics and Automation Electrical' => 'b',
                'Mechatronics and Automation PCB' => 'b',
                'Mechatronics and Automation Mechanical' => 'b',
                'Mechatronics and Automation Coding/Program' => 'b',
                'Socket' => 'a',
                'Mechanical Fabrication' => 'a',
            ];

            foreach ($map as $subName => $mainKey) {
                $normalized = $this->normalize($subName);

                $subcategory = SubCategory::all()->first(function ($s) use ($normalized) {
                    return $this->normalize($s->name) === $normalized;
                });

                if ($subcategory) {
                    $subcategory->main_category_id = $mains[$mainKey]->id;
                    $subcategory->save();
                } else {
                    // optional: log missing names to console when running seeder
                    $this->command->info("SubCategory not found: {$subName}");
                }
            }
        });
    }
}
