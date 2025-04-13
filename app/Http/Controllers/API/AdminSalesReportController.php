<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class AdminSalesReportController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
        ]);

        $start  = $request->start_date;
        $end    = $request->end_date;

        $orders = Order::with('order_details.product')
            ->where('transaction_status', 'paid')
            ->whereBetween('transaction_date', [$start, $end])
            ->get();

        $totalIncome        = $orders->sum('total_amount');
        $totalOrders        = $orders->count();
        $totalProductsSold  = $orders->flatMap->order_details->sum('quantity');

        $productSales = [];

        foreach ($orders as $order) {
            foreach ($order->order_details as $item) {
                $pid = $item->product_id;
                if (!isset($productSales[$pid])) {
                    $productSales[$pid] = [
                        'product_id'    => $pid,
                        'name'          => $item->product,
                        'total_sold'    => 0
                    ];
                }
                $productSales[$pid]['total_sold'] += $item->quantity;
            }
        }

        $bestSelling = collect($productSales)->sortByDesc('total_sold')->values();

        return ApiResponse::success([
            'filter_date'           => $start . ' - ' . $end,
            'total_income'          => $totalIncome,
            'total_orders'          => $totalOrders,
            'total_products_sold'   => $totalProductsSold,
            'best_selling_products' => $bestSelling,
        ], 'Sales report');
    }
}