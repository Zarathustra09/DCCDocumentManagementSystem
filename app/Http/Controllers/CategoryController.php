<?php

namespace App\Http\Controllers;

use App\Models\MainCategory;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('view category');
        try {
            $mainCategories = MainCategory::with(['subcategories' => function ($query) {
                $query->orderBy('name');
            }])->orderBy('name')->get();

            return view('category.index', compact('mainCategories'));
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load categories.');
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create category');
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:subcategories,code',
                'is_active' => 'boolean',
                'main_category_id' => 'required|exists:main_categories,id',
            ]);

            SubCategory::create([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'is_active' => $request->boolean('is_active'),
                'main_category_id' => $validated['main_category_id'],
            ]);

            return response()->json(['success' => true, 'message' => 'Subcategory created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create subcategory.'], 500);
        }
    }

    public function update(Request $request, SubCategory $subcategory)
    {
        $this->authorize('edit category');
        try {
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

            $subcategory->update([
                'name' => $validated['name'],
                'code' => strtoupper($validated['code']),
                'is_active' => $request->boolean('is_active'),
                'main_category_id' => $validated['main_category_id'],
            ]);

            return response()->json(['success' => true, 'message' => 'Subcategory updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update subcategory: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update subcategory: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(SubCategory $subcategory)
    {
        $this->authorize('delete category');
        try {
            // Check if subcategory has related documents
            $documentCount = $subcategory->documentRegistrationEntries()->count();
            if ($documentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete subcategory '{$subcategory->name}'. It has {$documentCount} related document(s). Please reassign or delete the documents first."
                ], 422);
            }

            $subcategory->delete();
            return response()->json(['success' => true, 'message' => 'Subcategory deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete subcategory: ' . $e->getMessage(), [
                'subcategory_id' => $subcategory->id,
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to delete subcategory: ' . $e->getMessage()], 500);
        }
    }

    public function storeMainCategory(Request $request)
    {
        $this->authorize('create category');
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:main_categories,name',
            ]);

            MainCategory::create($validated);

            return response()->json(['success' => true, 'message' => 'Main category created successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create main category: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create main category: ' . $e->getMessage()], 500);
        }
    }

    public function updateMainCategory(Request $request, MainCategory $mainCategory)
    {
        $this->authorize('edit category');
        try {
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('main_categories', 'name')->ignore($mainCategory->id),
                ],
            ]);

            $mainCategory->update($validated);

            return response()->json(['success' => true, 'message' => 'Main category updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update main category: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update main category: ' . $e->getMessage()], 500);
        }
    }

    public function destroyMainCategory(MainCategory $mainCategory)
    {
        $this->authorize('delete category');
        try {
            // Check if main category has subcategories
            $subcategoryCount = $mainCategory->subcategories()->count();
            if ($subcategoryCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete main category '{$mainCategory->name}'. It has {$subcategoryCount} subcategory(ies). Please delete or reassign the subcategories first."
                ], 422);
            }

            $mainCategory->delete();

            return response()->json(['success' => true, 'message' => 'Main category deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete main category: ' . $e->getMessage(), [
                'main_category_id' => $mainCategory->id,
                'exception' => $e
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to delete main category: ' . $e->getMessage()], 500);
        }
    }
}
