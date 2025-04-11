<?php

use App\Http\Controllers\API\AdminCategoryController;
use App\Http\Controllers\API\AdminDiscountController;
use App\Http\Controllers\API\AdminProductController;
use App\Http\Controllers\API\AdminProfileController;
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
use App\Models\CartItem;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
});

Route::get('/product', [ProductController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::resource('/cart', CartController::class);
    Route::put('/cart/{id}/update-qty', [CartController::class, 'updateQty']);
    Route::post('/checkout', [OrderController::class, 'checkout']);
    Route::post('/midtrans/snap-token', [PaymentController::class, 'createSnapToken']);
    Route::post('/midtrans/callback', [PaymentController::class, 'handleCallback']);

    Route::prefix('admin')->group(function () {
        Route::get('/profile', [AdminProfileController::class, 'index']);
        Route::put('/profile', [AdminProfileController::class, 'update']);

        Route::middleware(['role:Admin'])->group(function () {
            Route::resource('/product', AdminProductController::class);
            Route::resource('/category', AdminCategoryController::class);
            Route::resource('/discount', AdminDiscountController::class);
            Route::put('/discount/{id}/toggle', [AdminDiscountController::class, 'toggleStatus']);
        });
    });
});