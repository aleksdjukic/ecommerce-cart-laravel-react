<?php

namespace App\Services\Dashboard;

use App\Models\Order;
use Illuminate\Support\Carbon;

class DashboardChartService
{
    public function salesLast7Days(): array
    {
        $days = collect(range(0, 6))->map(function ($i) {
            return Carbon::today()->subDays($i);
        })->reverse();

        return [
            'labels' => $days->map(fn ($d) => $d->format('d.m'))->values(),
            'data' => $days->map(fn ($d) =>
                Order::whereDate('created_at', $d)->sum('total_price')
            )->values(),
        ];
    }
}
