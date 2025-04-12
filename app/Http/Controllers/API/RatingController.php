<?php

namespace App\Http\Controllers\API;

use App\Models\Order;
use App\Models\Rating;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function addRating(Request $request)
    {
        $request->validate([
            'product_id'    => 'required|exists:products,id',
            'rating'        => 'required|integer|between:1,5',
            'comment'       => 'nullable|string',
        ]);

        $user = Auth::user();
        $hasPurchased = Order::where('customer_id', $user->id)
            ->where('transaction_status', 'paid')
            ->whereHas('order_details', function ($query) use ($request) {
                $query->where('product_id', $request->product_id);
            })
            ->exists();

        if (!$hasPurchased) {
            return ApiResponse::error('You can only rate products you have purchased.', 403);
        }

        $rating = Rating::updateOrCreate(
            [
                'user_id'    => $user->id,
                'product_id' => $request->product_id
            ],
            [
                'rating'  => $request->rating,
                'comment' => $request->comment,
            ]
        );

        $message = $rating->wasRecentlyCreated ?
            'Rating added successfully.' : 'Rating updated successfuly.';

        return ApiResponse::success($rating, $message);
    }
}
