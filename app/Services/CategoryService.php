<?php

namespace App\Services;

use App\Interfaces\CategoryInterface;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\SubCategory;
use App\Models\MainCategory;

class CategoryService implements CategoryInterface
{
    /**
     * Create a new subcategory (validates the Request).
     *
     * @throws ValidationException
     */
    public function createSubCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:subcategories,code',
            'is_active' => 'boolean',
            'main_category_id' => 'required|exists:main_categories,id',
        ]);

        return SubCategory::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'is_active' => $request->boolean('is_active'),
            'main_category_id' => $validated['main_category_id'],
        ]);
    }

    /**
     * Update an existing subcategory (validates the Request).
     *
     * @throws ValidationException
     */
    public function updateSubCategory(Request $request, SubCategory $subcategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => [
                'required',
                'string',
                'max:3',
                Rule::unique('subcategories', 'code')->ignore($subcategory->id),
            ],
            'is_active' => 'boolean',
            'main_category_id' => 'required|exists:main_categories,id',
        ]);

        $subcategory->name = $validated['name'];
        $subcategory->code = strtoupper($validated['code']);
        $subcategory->is_active = $request->boolean('is_active');
        $subcategory->main_category_id = $validated['main_category_id'];
        $subcategory->save();

        return $subcategory;
    }

    /**
     * Delete a subcategory.
     */
    public function deleteSubCategory(SubCategory $subcategory)
    {
        return $subcategory->delete();
    }

    /**
     * Create a main category (validates the Request).
     *
     * @throws ValidationException
     */
    public function createMainCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:main_categories,name',
        ]);

        return MainCategory::create([
            'name' => $validated['name'],
        ]);
    }

    /**
     * Update a main category (validates the Request).
     *
     * @throws ValidationException
     */
    public function updateMainCategory(Request $request, MainCategory $mainCategory)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('main_categories', 'name')->ignore($mainCategory->id),
            ],
        ]);

        $mainCategory->name = $validated['name'];
        $mainCategory->save();

        return $mainCategory;
    }

    /**
     * Delete a main category.
     */
    public function deleteMainCategory(MainCategory $mainCategory)
    {
        return $mainCategory->delete();
    }
}
