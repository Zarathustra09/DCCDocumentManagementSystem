<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $this->authorize('view category');
        try {
            $categories = Category::orderBy('name')->get();
            return view('category.index', compact('categories'));
        } catch (\Exception $e) {
            return back()->withErrors('Failed to load categories.');
        }
    }

    public function store(Request $request)
    {
        $this->authorize('create category');
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:categories,code',
                'is_active' => 'boolean'
            ]);

            Category::create($request->all());

            return response()->json(['success' => true, 'message' => 'Category created successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create category.'], 500);
        }
    }

    public function update(Request $request, Category $category)
    {
        $this->authorize('edit category');
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:3|unique:categories,code,' . $category->id,
                'is_active' => 'boolean'
            ]);

            $category->update($request->all());

            return response()->json(['success' => true, 'message' => 'Category updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update category.'], 500);
        }
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete category');
        try {
            $category->delete();
            return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete category.'], 500);
        }
    }
}
