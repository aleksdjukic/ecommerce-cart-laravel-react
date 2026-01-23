<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CartService
{
    /**
     * Get existing cart or create new one for user
     */
    public function getOrCreateCart(User $user): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $user->id,
        ]);
    }

    /**
     * Add product to cart (or increment quantity)
     */
    public function addProduct(User $user, Product $product): void
    {
        $cart = $this->getOrCreateCart($user);

        DB::transaction(function () use ($cart, $product) {

            $item = $cart->items()
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $currentQuantity = $item?->quantity ?? 0;

            if ($currentQuantity + 1 > $product->stock_quantity) {
                Log::warning('Insufficient stock when adding to cart', [
                    'product_id' => $product->id,
                    'requested'  => $currentQuantity + 1,
                    'available'  => $product->stock_quantity,
                ]);

                throw new InsufficientStockException(
                    "Only {$product->stock_quantity} item(s) available for {$product->name}"
                );
            }

            if ($item) {
                $item->increment('quantity');
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity'   => 1,
                ]);
            }
        });
    }

    /**
     * Update cart item quantity
     */
    public function updateQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity < 1) {
            $this->removeItem($item);
            return;
        }

        DB::transaction(function () use ($item, $quantity) {

            $item->refresh();
            $product = $item->product()->lockForUpdate()->first();

            if ($quantity > $product->stock_quantity) {
                Log::warning('Insufficient stock when updating cart quantity', [
                    'product_id' => $product->id,
                    'requested'  => $quantity,
                    'available'  => $product->stock_quantity,
                ]);

                throw new InsufficientStockException(
                    "Only {$product->stock_quantity} item(s) available for {$product->name}"
                );
            }

            $item->update([
                'quantity' => $quantity,
            ]);
        });
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Clear entire cart
     */
    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
