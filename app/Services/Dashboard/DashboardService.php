<?php

namespace App\Services\Dashboard;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Carbon;

class DashboardService
{
    public function getStats(): array
    {
        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $ordersToday = Order::whereDate('created_at', $today)->get();
        $ordersMonth = Order::where('created_at', '>=', $monthStart)->get();

        return [
            'revenue' => [
                'today' => $ordersToday->sum('total_price'),
                'month' => $ordersMonth->sum('total_price'),
                'total' => Order::sum('total_price'),
            ],

            'orders' => [
                'today' => $ordersToday->count(),
                'month' => $ordersMonth->count(),
                'total' => Order::count(),
            ],

            'average_order_value' => Order::avg('total_price'),

            'low_stock_products' => Product::where('stock_quantity', '<=', 5)
                ->orderBy('stock_quantity')
                ->limit(5)
                ->get(),

            'latest_orders' => Order::with('items.product')
                ->latest()
                ->limit(5)
                ->get(),
        ];
    }
}
