<?php

use App\Http\Controllers\API\AdminCategoryController;
use App\Http\Controllers\API\AdminDiscountController;
use App\Http\Controllers\API\AdminProductController;
use App\Http\Controllers\API\AdminProfileController;
use App\Http\Controllers\API\AdminRatingController;
use App\Http\Controllers\API\AdminSalesReportController;
use App\Http\Controllers\API\AdminTransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DiscountController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RajaOngkirController;
use App\Http\Controllers\API\RatingController;
use App\Http\Controllers\API\WishlistController;
use App\Models\CartItem;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::get('/product', [ProductController::class, 'showProduct']);
Route::get('/product/{id}', [ProductController::class, 'showProductDetail']);
Route::post('/midtrans-callback', [PaymentController::class, 'handleCallback']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/cart', CartController::class);
    Route::put('/cart/{id}/update-qty', [CartController::class, 'updateQty']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::get('/order', [OrderController::class, 'orderHistory']);
    Route::post('/midtrans/snap-token', [PaymentController::class, 'createSnapToken']);
    Route::get('/wishlist', [WishlistController::class, 'getWishlist']);
    Route::post('/wishlist', [WishlistController::class, 'addToWishlist']);
    Route::delete('/wishlist/{product_id}', [WishlistController::class, 'removeWishlist']);
    Route::post('/rate-product', [RatingController::class, 'addRating']);

    Route::prefix('admin')->group(function () {
        Route::get('/profile', [AdminProfileController::class, 'index']);
        Route::put('/profile', [AdminProfileController::class, 'update']);

        Route::middleware(['role:Admin'])->group(function () {
            Route::resource('/product', AdminProductController::class);
            Route::resource('/category', AdminCategoryController::class);
            Route::resource('/discount', AdminDiscountController::class);
            Route::put('/discount/{id}/toggle', [AdminDiscountController::class, 'toggleStatus']);
            Route::get('/transaction', [AdminTransactionController::class, 'index']);
            Route::get('/transaction/{id}', [AdminTransactionController::class, 'showDetailTransaction']);
            Route::get('/rating', [AdminRatingController::class, 'index']);
            Route::get('/rating/{id}', [AdminRatingController::class, 'showDetailRating']);
            Route::delete('/rating/{id}', [AdminRatingController::class, 'destroyRating']);
            Route::get('/sales-report', [AdminSalesReportController::class, 'index']);
        });
    });
});