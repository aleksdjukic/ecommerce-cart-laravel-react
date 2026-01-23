<?php

namespace App\Services\Reports;

use App\Mail\DailySalesReportMail;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class DailySalesReportService
{
    public function send(): void
    {
        $yesterday = Carbon::yesterday();

        $orders = Order::query()
            ->whereDate('created_at', $yesterday)
            ->get();

        $totalRevenue = $orders->sum('total_price');
        $ordersCount  = $orders->count();

        Mail::to(config('shop.admin_email'))
            ->send(new DailySalesReportMail(
                date: $yesterday,
                totalRevenue: $totalRevenue,
                ordersCount: $ordersCount,
            ));
    }
}
