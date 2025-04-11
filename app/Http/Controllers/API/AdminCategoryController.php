<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;

class AdminCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->paginate(10);
        return ApiResponse::success(CategoryResource::collection($categories), 'Show all category');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'  => 'required|unique:categories'
        ]);

        $category = Category::create([
            'category'  => $validated['category']
        ]);

        return ApiResponse::success(new CategoryResource($category), 'Category created successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error('Data not found.', 404);
        }

        $validated = $request->validate([
            'category'  => 'required|unique:categories,category,' . $category->id
        ]);

        $category->category = $validated['category'];
        $category->save();

        return ApiResponse::success(new CategoryResource($category), 'Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return ApiResponse::error('Data not found.', 404);
        }

        $category->delete();

        return ApiResponse::success(new CategoryResource($category), 'Category delete succesfully.');
    }
}