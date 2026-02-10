<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    public function saved(Product $product): void
    {
        Cache::forget('products.index');
    }

    public function deleted(Product $product): void
    {
        Cache::forget('products.index');
    }
}
