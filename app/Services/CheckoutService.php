<?php

namespace App\Services;

use App\Events\ProductStockLow;
use App\Exceptions\InsufficientStockException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutService
{
    public function checkout(User $user): Order
    {
        return DB::transaction(function () use ($user) {

            $cart = $user->cart()
                ->with('items.product')
                ->lockForUpdate()
                ->firstOrFail();

            if ($cart->items->isEmpty()) {
                throw new \RuntimeException('Cart is empty');
            }

            $totalPrice = 0;

            foreach ($cart->items as $item) {
                $product = $item->product()
                    ->lockForUpdate()
                    ->first();

                if ($item->quantity > $product->stock_quantity) {
                    throw new InsufficientStockException(
                        "Only {$product->stock_quantity} item(s) available for {$product->name}"
                    );
                }

                // decrement stock
                $product->decrement('stock_quantity', $item->quantity);

                // 🔔 LOW STOCK EVENT
                if ($product->stock_quantity <= config('shop.low_stock_threshold')) {
                    event(new ProductStockLow($product));
                }

                $totalPrice += $item->quantity * $product->price;
            }

            $order = Order::create([
                'user_id'     => $user->id,
                'total_price' => $totalPrice,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'price'      => $item->product->price,
                    'quantity'   => $item->quantity,
                ]);
            }

            // clear cart
            $cart->items()->delete();

            Log::info('Checkout completed', [
                'order_id' => $order->id,
                'user_id'  => $user->id,
                'total'    => $totalPrice,
            ]);

            Cache::forget('products.index');

            return $order;
        });
    }
}
