<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read \App\Models\Cart $cart
 * @property-read \App\Models\Product $product
 */
class CartItem extends Model
{
    protected $fillable = ['cart_id', 'product_id', 'quantity'];

    /**
     * @return BelongsTo<\App\Models\Cart, $this>
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * @return BelongsTo<\App\Models\Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
