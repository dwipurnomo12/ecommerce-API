<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // search by keywoard
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Short product
        if ($request->sort == 'price_asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort == 'price_desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(10);
        return ApiResponse::success(ProductResource::collection($products), 'Product list fetched.');
    }

    public function showProductDetail(String $id)
    {
        $product = Product::with(['ratings', 'product_galleries', 'category', 'posted_by'])->find($id);
        if (!$product) {
            return ApiResponse::error('Data not found', 404);
        }

        return ApiResponse::success(new ProductResource($product), 'Show detail product');
    }
}
