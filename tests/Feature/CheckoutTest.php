<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_checkout_successfully(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'price' => 100,
            'stock_quantity' => 10,
        ]);

        $cart = $user->cart()->create();

        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/checkout');

        $response->assertOk()
            ->assertJsonStructure(['order_id']);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_price' => 200,
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 8,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'product_id' => $product->id,
        ]);
    }

    public function test_checkout_fails_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();

        $product = Product::factory()->create([
            'stock_quantity' => 1,
        ]);

        $cart = $user->cart()->create();

        $cart->items()->create([
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response = $this
            ->actingAs($user)
            ->postJson('/api/checkout');

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => "Only 1 item(s) available for {$product->name}",
            ]);
    }
}
