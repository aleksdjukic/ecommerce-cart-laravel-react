<?php

namespace App\Services\Reports;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

class DailySalesPdfService
{
    public function generate()
    {
        $date = Carbon::yesterday();
        $orders = Order::whereDate('created_at', $date)->get();

        return Pdf::loadView('pdf.daily-sales', [
            'date' => $date,
            'orders' => $orders,
            'total' => $orders->sum('total_price'),
        ])->download('daily-sales.pdf');
    }
}
