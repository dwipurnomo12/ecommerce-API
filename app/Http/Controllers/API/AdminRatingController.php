<?php

namespace App\Http\Controllers\API;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\RatingResource;
use App\Models\Rating;
use Illuminate\Http\Request;

class AdminRatingController extends Controller
{
    public function index()
    {
        $ratings = Rating::with(['customer', 'product'])->latest()->paginate(10);
        return ApiResponse::success(RatingResource::collection($ratings), 'Show all ratings');
    }

    public function showDetailRating($id)
    {
        $rating = Rating::with(['customer', 'product'])->find($id);
        if (!$rating) {
            return ApiResponse::error('Data not found.', 404);
        }

        return ApiResponse::success(new RatingResource($rating), 'Show detail rating');
    }

    public function destroyRating($id)
    {
        $rating = Rating::find($id);
        if (!$rating) {
            return ApiResponse::error('Data not found.', 404);
        }
        $rating->delete();

        return ApiResponse::success(null, 'Review deleted successfully.');
    }
}