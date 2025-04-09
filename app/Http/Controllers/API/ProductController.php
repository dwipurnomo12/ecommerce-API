<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(10);
        return ApiResponse::success(
            ProductResource::collection($products)->response()->getData(true),
            'Show all products'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'featured_image'    => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'name'              => 'required',
            'slug'              => 'required|unique:products',
            'description'       => 'required|string',
            'price'             => 'required|numeric',
        ]);

        $imagePath = $request->file('featured_image')->store('products', 'public');
        $product = Product::create([
            'featured_image' => $imagePath,
            'name'           => $validated['name'],
            'slug'           => Str::slug($validated['slug']),
            'description'    => $validated['description'],
            'price'          => $validated['price'],
            'posted_by'      => auth()->user()->id,
        ]);

        return ApiResponse::success(new ProductResource($product), 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::error('Data not found', 404);
        }

        return ApiResponse::success(new ProductResource($product), 'Show detail product');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return ApiResponse::error('Data not found', 404);
        }

        $validated = $request->validate([
            'featured_image'    => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            'name'              => 'required',
            'slug'              => 'required|unique:products,slug,' . $product->id,
            'description'       => 'required|string',
            'price'             => 'required|numeric',
        ]);

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image && Storage::disk('public')->exist($product->featured_image)) {
                Storage::disk('public')->delete($product->featured_iimage);
            }

            $imagePath = $request->file('featured_image')->store('products', 'public');
            $product->featured_image = $imagePath;
        }

        $product->name = $validated['name'];
        $product->slug = Str::slug($validated['slug']);
        $product->description = $validated['description'];
        $product->price = $validated['price'];
        $product->save();

        return ApiResponse::success(new ProductResource($product), 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return ApiResponse::error('Data not found', 404);
        }
        $product->delete();

        return ApiResponse::success(new ProductResource($product), 'Product delete succesfully.');
    }
}