<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;

class OrderController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method'   => 'required|string',
        ]);

        $cart = Cart::with('cartItems.product')->where('user_id', auth()->user()->id)->first();
        if (!$cart || $cart->cartItems->isEmpty()) {
            return ApiResponse::error('Cart is empty.', 400);
        }

        $totalAmount = 0;
        foreach ($cart->cartItems as $item) {
            $totalAmount += $item->product->price * $item->quantity;
        }

        $order = Order::create([
            'invoice_code'       => 'INV-' . strtoupper(Str::random(5)),
            'transaction_date'   => now(),
            'transaction_status' => 'pending ',
            'total_amount'       => $totalAmount,
            'customer_id'        => auth()->user()->id,
            'shipping_address'   => $request->shipping_address,
            'payment_method'     => $request->payment_method,
        ]);

        foreach ($cart->cartItems as $item) {
            OrderDetail::create([
                'order_id'  => $order->id,
                'product_id' => $item->product->id,
                'product'   => $item->product->name,
                'quantity'  => $item->quantity,
                'price'     => $item->product->price
            ]);
        }

        $cart->delete();

        return ApiResponse::success($order, 'Checkout successful, please make payment.');
    }
}