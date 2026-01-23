<?php

use App\Exports\OrdersCsvExport;
use App\Http\Controllers\DashboardController;
use App\Services\Reports\DailySalesPdfService;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::middleware(['auth'])->group(function () {

     // SPA pages
    Route::get('/products', function () {
        return Inertia::render('Products/Index');
    })->name('products.index');

    Route::get('/cart', function () {
        return Inertia::render('Cart/Index');
    })->name('cart.index');

    Route::get('/checkout', function () {
        return Inertia::render('Checkout/Index');
    })->name('checkout.index');


    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Exports

    Route::get('/dashboard/export/orders', [DashboardController::class, 'exportOrders'])
        ->name('dashboard.export.orders');

    Route::get('/dashboard/export/low-stock', [DashboardController::class, 'exportLowStock'])
        ->name('dashboard.export.low-stock');

    Route::get('/export/daily-sales/pdf', fn () =>
        app(DailySalesPdfService::class)->generate()
    )->name('export.daily-sales.pdf');
});

require __DIR__.'/auth.php';
