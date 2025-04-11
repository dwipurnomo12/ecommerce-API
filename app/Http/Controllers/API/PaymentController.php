<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function createSnapToken(Request $request)
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $request->validate([
            'order_id' => 'required|exists:orders,id'
        ]);

        $order = Order::findOrFail($request->order_id);
        $itemDetails = [];

        foreach ($order->order_details as $item) {
            $itemDetails[] = [
                'id'       => $item->product_id,
                'price'    => (int) $item->price,
                'quantity' => (int) $item->quantity,
                'name'     => $item->product,
            ];
        }

        $payload = array(
            'transaction_details'   => array(
                'order_id'      => $order->invoice_code,
                'gross_amount'  => $order->total_amount,
            ),
            'item_details' => $itemDetails,
            'customer_details' => array(
                'first_name'    => $order->customer->name,
                'email'         => $order->customer->email
            ),
        );

        $snapToken = Snap::getSnapToken($payload);
        return response()->json([
            'snap_token'    => $snapToken
        ]);
    }

    public function handleCallback(Request $request)
    {
        $notif = new \Midtrans\Notification();

        $transactionStatus = $notif->transaction_status;
        $orderId           = $notif->order_id;

        $serverKey      = config('midtrans.server_key');
        $orderId        = $request->order_id;
        $grossAmount    = $request->gross_amount;
        $signatureKey   = $request->signature_key;

        $hashed =  hash("sha512", $orderId . $grossAmount . $serverKey);
        if ($signatureKey !== $hashed) {
            return ApiResponse::error('Invalid signature', 403);
        }

        $order = Order::where('invoice_code', $orderId)->first();
        if (!$order) {
            return ApiResponse::error('Data not found', 404);
        }

        switch ($transactionStatus) {
            case 'capture':
            case 'settlement':
                $order->transaction_status = 'paid';
                break;
            case 'pending':
                $order->transaction_status = 'pending';
                break;
            case 'deny':
            case 'expire':
            case 'cancel':
                $order->transaction_status = 'unpaid';
                break;
        }

        $order->save();

        return response()->json(['message' => 'Notification handled']);
    }
}