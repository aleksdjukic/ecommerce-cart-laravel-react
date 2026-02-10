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
    private function reservationExpiry(): \DateTimeInterface
    {
        $minutes = (int) config('shop.cart_reservation_minutes', 20);

        return now()->addMinutes($minutes);
    }

    public function purgeExpiredReservations(Cart $cart): void
    {
        $cart->items()
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '<', now())
            ->delete();
    }

    private function reservedQuantityForProduct(Product $product, Cart $excludeCart): int
    {
        return (int) CartItem::query()
            ->where('product_id', $product->id)
            ->whereNotNull('reserved_until')
            ->where('reserved_until', '>=', now())
            ->where('cart_id', '!=', $excludeCart->id)
            ->sum('quantity');
    }

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
        $this->purgeExpiredReservations($cart);

        DB::transaction(function () use ($cart, $product) {

            /** @var CartItem|null $item */
            $item = $cart->items()
                ->where('product_id', $product->id)
                ->lockForUpdate()
                ->first();

            $currentQuantity = $item ? $item->quantity : 0;
            $reservedQuantity = $this->reservedQuantityForProduct($product, $cart);
            $availableStock = $product->stock_quantity - $reservedQuantity;

            if ($currentQuantity + 1 > $availableStock) {
                Log::warning('Insufficient stock when adding to cart', [
                    'product_id' => $product->id,
                    'requested' => $currentQuantity + 1,
                    'available' => $availableStock,
                ]);

                throw new InsufficientStockException(
                    "Only {$availableStock} item(s) available for {$product->name}"
                );
            }

            if ($item) {
                $item->increment('quantity');
                $item->update([
                    'reserved_until' => $this->reservationExpiry(),
                ]);
            } else {
                $cart->items()->create([
                    'product_id' => $product->id,
                    'quantity' => 1,
                    'reserved_until' => $this->reservationExpiry(),
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
            /** @var Product $product */
            $product = $item->product()->lockForUpdate()->first();

            $this->purgeExpiredReservations($item->cart);
            $reservedQuantity = $this->reservedQuantityForProduct($product, $item->cart);
            $availableStock = $product->stock_quantity - $reservedQuantity;

            if ($quantity > $availableStock) {
                Log::warning('Insufficient stock when updating cart quantity', [
                    'product_id' => $product->id,
                    'requested' => $quantity,
                    'available' => $availableStock,
                ]);

                throw new InsufficientStockException(
                    "Only {$availableStock} item(s) available for {$product->name}"
                );
            }

            $item->update([
                'quantity' => $quantity,
                'reserved_until' => $this->reservationExpiry(),
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
