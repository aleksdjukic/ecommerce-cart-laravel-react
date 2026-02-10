<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items
 */
class Cart extends Model
{
    protected $fillable = ['user_id'];

    /**
     * @return HasMany<\App\Models\CartItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalPriceAttribute(): float
    {
        return $this->items->sum(
            fn ($item) => $item->quantity * $item->product->price
        );
    }
}
