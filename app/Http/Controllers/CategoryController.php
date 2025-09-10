<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        return view('category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:categories,code',
            'is_active' => 'boolean'
        ]);

        Category::create($request->all());

        return response()->json(['success' => true, 'message' => 'Category created successfully']);
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:3|unique:categories,code,' . $category->id,
            'is_active' => 'boolean'
        ]);

        $category->update($request->all());

        return response()->json(['success' => true, 'message' => 'Category updated successfully']);
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->json(['success' => true, 'message' => 'Category deleted successfully']);
    }
}
