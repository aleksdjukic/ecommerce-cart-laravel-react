<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'items' => $this->items->map(fn ($item) => [
                'id'       => $item->id,
                'quantity' => $item->quantity,
                'product'  => new ProductResource($item->product),
            ]),
        ];
    }
}
