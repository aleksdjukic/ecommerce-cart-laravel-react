<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_reservation_blocks_other_carts(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 1,
        ]);

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $service = app(CartService::class);

        $service->addProduct($userA, $product);

        $this->expectException(InsufficientStockException::class);
        $service->addProduct($userB, $product);
    }

    public function test_expired_reservation_is_released(): void
    {
        $product = Product::factory()->create([
            'stock_quantity' => 1,
        ]);

        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $service = app(CartService::class);

        $service->addProduct($userA, $product);

        $item = $userA->cart->items()->firstOrFail();
        $item->update([
            'reserved_until' => now()->subMinute(),
        ]);

        $service->addProduct($userB, $product);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userB->cart->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }
}
