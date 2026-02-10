<?php

namespace App\Http\Requests;

use App\Models\CartItem;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var CartItem|null $item */
        $item = $this->route('item');

        if (! $item) {
            return false;
        }

        return $item->cart->user_id === $this->user()->id;
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
}
