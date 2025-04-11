<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\CartItem;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartItems = Cart::with(['cartItems.product'])->where('user_id', auth()->user()->id)->latest()->get();
        return ApiResponse::success(CartResource::collection($cartItems), 'Show cart item');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $cart = Cart::firstOrCreate([
            'user_id'   => auth()->user()->id
        ]);

        $existingItem = $cart->cartItems()->where('product_id', $request->product_id)->first();
        if ($existingItem) {
            $existingItem->quantity += $request->input('quantity', 1);
            $existingItem->save();
        } else {
            $cart->cartItems()->create([
                'product_id'    => $request->product_id,
                'quantity'      => $request->input('quantity', 1)
            ]);
        }

        return ApiResponse::success(null, 'Item added to cart.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateQty(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = CartItem::whereHas('cart', function ($q) {
            $q->where('user_id', auth()->id());
        })->findOrFail($id);

        $item->update([
            'quantity' => $request->quantity,
        ]);

        return ApiResponse::success($item, 'Quantity updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return ApiResponse::error('Data not found', 404);
        }
        $cart->delete();

        return ApiResponse::success(new CartResource($cart), 'Cart items delete succesfully.');
    }
}