<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\CheckoutService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function store(Request $request, CheckoutService $checkoutService)
    {
        $order = $checkoutService->checkout($request->user());

        return response()->json([
            'message'  => 'Order placed successfully',
            'order_id' => $order->id,
        ]);
    }
}
