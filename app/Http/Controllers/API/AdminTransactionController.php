<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        $query  = Order::with('customer');

        if ($status) {
            $query->where('transaction_status', $status);
        }

        $orders = $query->latest()->paginate(10);
        return ApiResponse::success(OrderResource::collection($orders), 'Show list transaction');
    }

    public function showDetailTransaction(String $id)
    {
        $order = Order::with(['customer', 'order_details', 'discount'])->find($id);
        if (!$order) {
            return ApiResponse::error('Data not found', 404);
        }

        return ApiResponse::success(new OrderResource($order), 'Show detail transaction.');
    }
}
