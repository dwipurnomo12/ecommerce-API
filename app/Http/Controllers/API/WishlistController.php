<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;
use PHPUnit\TextUI\XmlConfiguration\ReplaceRestrictDeprecationsWithIgnoreDeprecations;

class WishlistController extends Controller
{
    public function getWishlist()
    {
        $user = Auth::user();
        $wishlist = Wishlist::with('product')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return ApiResponse::success(WishlistResource::collection($wishlist), 'Show wishlist customer');
    }

    public function addToWishlist(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        $productId = $request->product_id;

        $existing = Wishlist::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            return ApiResponse::error('The product is already in the wishlist.', 400);
        }

        $wishlist = Wishlist::create([
            'user_id'       => $user->id,
            'product_id'    => $productId
        ]);

        return ApiResponse::success(new WishlistResource($wishlist), 'Product added to wishlist.');
    }

    public function removeWishlist($product_id)
    {
        $user = Auth::user();

        $wishlist = Wishlist::where('user_id', $user->id)
            ->where('product_id', $product_id)
            ->first();

        if (!$wishlist) {
            return ApiResponse::error('Wishlist item not found.', 404);
        }

        $wishlist->delete();

        return ApiResponse::success(null, 'Product removed from wishlist.');
    }
}
