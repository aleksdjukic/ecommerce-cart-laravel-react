<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConcurrencyCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_stock_cannot_go_negative_with_parallel_checkouts(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $product = Product::factory()->create([
            'stock_quantity' => 1,
            'price' => 100,
        ]);

        // oba korisnika dodaju isti proizvod
        $user1->cart()->create()->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $user2->cart()->create()->items()->create([
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // prvi checkout prolazi
        $this->actingAs($user1)->postJson('/api/checkout')
            ->assertSuccessful();

        // drugi checkout mora pasti
        $this->actingAs($user2)->postJson('/api/checkout')
            ->assertStatus(422);

        $this->assertEquals(0, $product->fresh()->stock_quantity);
    }
}
