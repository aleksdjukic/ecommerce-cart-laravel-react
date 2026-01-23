<?php

namespace Tests\Feature;

use App\Events\ProductStockLow;
use App\Jobs\SendLowStockNotificationJob;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class LowStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_dispatches_job(): void
    {
        Queue::fake();

        $product = Product::factory()->create([
            'stock_quantity' => 2,
        ]);

        $listener = new \App\Listeners\DispatchLowStockNotification();

        $listener->handle(
            new \App\Events\ProductStockLow($product)
        );

        Queue::assertPushed(
            \App\Jobs\SendLowStockNotificationJob::class
        );
    }

}
