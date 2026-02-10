<?php

namespace App\Services;

use App\Events\ProductStockLow;
use App\Exceptions\InsufficientStockException;
use App\Mail\OrderConfirmationMail;
use App\Models\Order;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CheckoutService
{
    public function checkout(User $user): Order
    {
        return DB::transaction(function () use ($user) {

            $cart = $user->cart()
                ->with('items.product')
                ->lockForUpdate()
                ->firstOrFail();

            app(CartService::class)->purgeExpiredReservations($cart);

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

                $threshold = (int) config('shop.low_stock_threshold', 5);
                $oldStock = $product->stock_quantity;
                $newStock = $oldStock - $item->quantity;

                // decrement stock
                $product->decrement('stock_quantity', $item->quantity);

                // 🔔 LOW STOCK EVENT (only on threshold crossing)
                if ($oldStock > $threshold && $newStock <= $threshold) {
                    event(new ProductStockLow($product));

                    Log::warning('Product stock low', [
                        'product_id' => $product->id,
                        'stock' => $newStock,
                    ]);
                }

                $totalPrice += $item->quantity * $product->price;
            }

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => $totalPrice,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                ]);
            }

            // clear cart
            $cart->items()->delete();

            $order->load('items.product');

            DB::afterCommit(function () use ($order, $user) {
                Mail::to($user->email)->send(new OrderConfirmationMail($order));
            });

            Log::info('Checkout completed', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'total' => $totalPrice,
            ]);

            return $order;
        });
    }
}
