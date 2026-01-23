<?php

namespace App\Providers;

use App\Events\ProductStockLow;
use App\Listeners\DispatchLowStockNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ProductStockLow::class => [
            DispatchLowStockNotification::class,
        ],
    ];
}
