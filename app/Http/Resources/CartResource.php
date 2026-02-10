<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Cart
 */
class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\CartItem> $items */
        $items = $this->items;

        return [
            'id' => $this->id,

            'items' => $items->map(fn ($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'reserved_until' => $item->reserved_until?->toIso8601String(),
                'product' => new ProductResource($item->product),
            ]),

            'total_price' => round($this->total_price, 2),
        ];
    }
}
