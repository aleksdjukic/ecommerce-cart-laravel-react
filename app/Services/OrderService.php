<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function checkout(User $user): Order
    {
        $cart = $user->cart()->with('items.product')->firstOrFail();

        return DB::transaction(function () use ($cart, $user) {

            $total = 0;

            $order = Order::create([
                'user_id' => $user->id,
                'total_price' => 0,
            ]);

            foreach ($cart->items as $item) {
                $product = $item->product()->lockForUpdate()->first();

                if ($item->quantity > $product->stock_quantity) {
                    throw new \RuntimeException('Stock changed during checkout.');
                }

                $product->decrement('stock_quantity', $item->quantity);

                OrderItem::create([
                    'order_id'  => $order->id,
                    'product_id'=> $product->id,
                    'price'     => $product->price,
                    'quantity'  => $item->quantity,
                ]);

                $total += $product->price * $item->quantity;
            }

            $order->update(['total_price' => $total]);

            $cart->items()->delete();

            Log::info('Order placed', [
                'order_id' => $order->id,
                'user_id'  => $user->id,
                'total'    => $total,
            ]);

            return $order;
        });
    }
}
