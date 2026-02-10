<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ProductCacheInvalidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_products_index_cache_is_cleared_on_product_update(): void
    {
        $product = Product::factory()->create();

        Cache::put('products.index', ['stub' => 'value'], 60);
        $this->assertTrue(Cache::has('products.index'));

        $product->update(['name' => 'Updated name']);

        $this->assertFalse(Cache::has('products.index'));
    }
}
