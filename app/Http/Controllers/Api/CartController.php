<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddToCartRequest;
use App\Http\Requests\UpdateCartItemRequest;
use App\Http\Resources\CartResource;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CartController extends Controller
{
    use AuthorizesRequests;

    public function show(Request $request, CartService $cartService)
    {
        $cart = $cartService->getOrCreateCart($request->user());

        return new CartResource($cart->load('items.product'));
    }

    public function store(AddToCartRequest $request, CartService $cartService)
    {
        $cartService->addProduct(
            $request->user(),
            $request->product()
        );

        return response()->json(['message' => 'Item added to cart']);
    }

    public function update(
        UpdateCartItemRequest $request,
        CartItem $item,
        CartService $cartService
    ) {
        $this->authorize('update', $item);

        $cartService->updateQuantity($item, $request->quantity);

        return response()->json(['message' => 'Cart updated']);
    }

    public function destroy(CartItem $item, CartService $cartService)
    {
        $this->authorize('delete', $item);

        $cartService->removeItem($item);

        return response()->json(['message' => 'Item removed']);
    }
}
