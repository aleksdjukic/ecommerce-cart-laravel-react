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

        $ordersTodayRevenue = Order::whereDate('created_at', $today)
            ->sum('total_price');
        $ordersTodayCount = Order::whereDate('created_at', $today)->count();

        $ordersMonthRevenue = Order::where('created_at', '>=', $monthStart)
            ->sum('total_price');
        $ordersMonthCount = Order::where('created_at', '>=', $monthStart)
            ->count();

        return [
            'revenue' => [
                'today' => $ordersTodayRevenue,
                'month' => $ordersMonthRevenue,
                'total' => Order::sum('total_price'),
            ],

            'orders' => [
                'today' => $ordersTodayCount,
                'month' => $ordersMonthCount,
                'total' => Order::count(),
            ],

            'average_order_value' => Order::avg('total_price'),

            'low_stock_products' => Product::where(
                'stock_quantity',
                '<=',
                (int) config('shop.low_stock_threshold', 5)
            )
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
