<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Policies\CartItemPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Gate::policy(CartItem::class, CartItemPolicy::class);
    }
}
