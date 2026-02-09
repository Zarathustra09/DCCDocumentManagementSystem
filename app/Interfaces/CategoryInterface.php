<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Models\SubCategory;
use App\Models\MainCategory;

interface CategoryInterface
{
    // Create/Update/Delete subcategory
    public function createSubCategory(Request $request);
    public function updateSubCategory(Request $request, SubCategory $subcategory);
    public function deleteSubCategory(SubCategory $subcategory);

    // Create/Update/Delete main category
    public function createMainCategory(Request $request);
    public function updateMainCategory(Request $request, MainCategory $mainCategory);
    public function deleteMainCategory(MainCategory $mainCategory);
}
