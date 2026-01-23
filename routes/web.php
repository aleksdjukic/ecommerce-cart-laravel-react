<?php

use App\Http\Controllers\DashboardController;
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

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/export/orders/csv', fn () =>
        app(\App\Exports\OrdersCsvExport::class)->download()
    );

    Route::get('/export/daily-sales/pdf', fn () =>
        app(\App\Services\Reports\DailySalesPdfService::class)->generate()
    );
});

require __DIR__.'/auth.php';
