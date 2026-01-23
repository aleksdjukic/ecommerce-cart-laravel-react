<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index()
    {
        $products = Cache::remember(
            'products.index',
            now()->addMinutes(5),
            fn () => Product::query()
                ->where('stock_quantity', '>', 0)
                ->paginate(10)
        );

        return ProductResource::collection($products);
    }
}
