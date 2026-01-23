<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {

    // Products
    Route::get('/products', [ProductController::class, 'index'])
        ->name('api.products.index');

    // Cart
    Route::get('/cart', [CartController::class, 'show'])
        ->name('api.cart.show');

    Route::post('/cart/items', [CartController::class, 'store'])
        ->name('api.cart.items.store');

    Route::patch('/cart/items/{item}', [CartController::class, 'update'])
        ->name('api.cart.items.update');

    Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])
        ->name('api.cart.items.destroy');

    // Checkout
    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('api.checkout.store');
    });
