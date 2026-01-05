<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class RemoveMechatronicsParentCategorySeeder extends Seeder
{
    public function run(): void
    {
        // Find and delete the parent "Mechatronics and Automation" category
        $categoryToRemove = Category::where('name', 'Mechatronics and Automation')
                                  ->where('code', 'MA')
                                  ->first();

        if ($categoryToRemove) {
            // Check if there are any document registration entries using this category
            $entriesCount = $categoryToRemove->documentRegistrationEntries()->count();

            if ($entriesCount > 0) {
                $this->command->warn("Warning: Found {$entriesCount} document registration entries using this category. Category will still be removed, but you may need to update those entries.");
            }

            $categoryToRemove->delete();
            $this->command->info("Successfully removed 'Mechatronics and Automation' category (MA)");
        } else {
            $this->command->info("Category 'Mechatronics and Automation' (MA) not found in database");
        }
    }
}
