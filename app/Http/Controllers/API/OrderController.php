<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Discount;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function orderHistory()
    {
        $orderHistory = Order::with(['customer', 'discount'])
            ->where('customer_id', auth()->user()->id)
            ->latest()
            ->paginate(10);

        return ApiResponse::success(OrderResource::collection($orderHistory), 'Show order history customer');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'payment_method'   => 'required|string',
            'code_discount'    => 'nullable|string|exists:discounts,discount_code',
        ]);

        $cart = Cart::with('cartItems.product')->where('user_id', auth()->user()->id)->first();
        if (!$cart || $cart->cartItems->isEmpty()) {
            return ApiResponse::error('Cart is empty.', 400);
        }

        $totalAmount = 0;
        foreach ($cart->cartItems as $item) {
            $totalAmount += $item->product->price * $item->quantity;
        }

        $discount = null;
        $discountAmount = 0;

        if ($request->filled('code_discount')) {
            $discount = Discount::where('discount_code', $request->code_discount)
                ->where('is_active', true)
                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->first();

            if ($discount) {
                $discountAmount = ($totalAmount * $discount->discount_amount) / 100;
                $totalAmount -= $discountAmount;
                $totalAmount = max($totalAmount, 0);
            } else {
                return ApiResponse::error('Invalid or expired discount code.', 400);
            }
        }

        $order = Order::create([
            'invoice_code'       => 'INV-' . strtoupper(Str::random(5)),
            'transaction_date'   => now(),
            'transaction_status' => 'pending ',
            'total_amount'       => $totalAmount,
            'customer_id'        => auth()->user()->id,
            'shipping_address'   => $request->shipping_address,
            'payment_method'     => $request->payment_method,
            'discount_id'        => $discount?->id
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

        return ApiResponse::success([
            'order'             => $order,
            'discount_applied'  => $discount ? $discount->discount_code : null,
            'discount_value'    => $discountAmount,
        ], 'Checkout successful, please make payment.');
    }
}
