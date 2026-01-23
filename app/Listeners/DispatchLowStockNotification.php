<?php

namespace App\Listeners;

use App\Events\ProductStockLow;
use App\Jobs\SendLowStockNotificationJob;

class DispatchLowStockNotification
{
    public function handle(ProductStockLow $event): void
    {
        SendLowStockNotificationJob::dispatch($event->product);
    }
}
