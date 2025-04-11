<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\ProductGallery;
use Illuminate\Support\Facades\Storage;

class AdminProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with(['posted_by', 'category'])->latest()->paginate(10);
        return ApiResponse::success(ProductResource::collection($products), 'Show all products');
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
            'gallery'           => 'nullable|array',
            'gallery.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
            'category_id'       => 'required'
        ]);

        $imagePath = $request->file('featured_image')->store('featured_image', 'public');
        $product = Product::create([
            'featured_image' => $imagePath,
            'name'           => $validated['name'],
            'slug'           => Str::slug($validated['slug']),
            'description'    => $validated['description'],
            'price'          => $validated['price'],
            'posted_by'      => auth()->user()->id,
            'category_id'    => $validated['category_id']
        ]);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $galleryImage) {
                $galleryPath = $galleryImage->store('gallery_product', 'public');

                ProductGallery::create([
                    'product_id' => $product->id,
                    'image'      => $galleryPath,
                ]);
            }
        }

        $product->load(['posted_by', 'category', 'product_galleries']);
        return ApiResponse::success(new ProductResource($product), 'Product created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = Product::with('product_galleries')->find($id);
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
            'status'            => 'required',
            'gallery'           => 'nullable|array',
            'gallery.*'         => 'image|mimes:jpg,jpeg,png|max:2048',
            'category_id'       => 'required'
        ]);

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image && Storage::disk('public')->exist($product->featured_image)) {
                Storage::disk('public')->delete($product->featured_image);
            }

            $imagePath = $request->file('featured_image')->store('featured_image', 'public');
            $product->featured_image = $imagePath;
        }

        if ($request->hasFile('gallery')) {
            foreach ($product->galleries as $gallery) {
                if (Storage::disk('public')->exists($gallery->image)) {
                    Storage::disk('public')->delete($gallery->image);
                }
                $gallery->delete();
            }

            foreach ($request->file('gallery') as $galleryImage) {
                $galleryPath = $galleryImage->store('gallery_product', 'public');

                ProductGallery::create([
                    'product_id' => $product->id,
                    'image'      => $galleryPath,
                ]);
            }
        }

        $product->name = $validated['name'];
        $product->slug = Str::slug($validated['slug']);
        $product->description = $validated['description'];
        $product->price = $validated['price'];
        $product->status = $validated['status'];
        $product->category_id = $validated['category_id'];
        $product->save();

        $product->load(['posted_by', 'category', 'product_galleries']);
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