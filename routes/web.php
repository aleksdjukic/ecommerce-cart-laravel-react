<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {

    Route::get('/products', function () {
        return Inertia::render('Products/Index');
    })->name('products.index');

    Route::get('/cart', function () {
        return Inertia::render('Cart/Index');
    })->name('cart.index');

    Route::get('/checkout', function () {
        return Inertia::render('Checkout/Index');
    })->name('checkout.index');
});

require __DIR__.'/auth.php';
