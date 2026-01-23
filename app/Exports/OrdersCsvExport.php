<?php

namespace App\Exports;

use App\Models\Order;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrdersCsvExport
{
    public function download(): StreamedResponse
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Total', 'Created at']);

            Order::cursor()->each(function ($order) use ($handle) {
                fputcsv($handle, [
                    $order->id,
                    $order->total_price,
                    $order->created_at,
                ]);
            });

            fclose($handle);
        }, 'orders.csv');
    }
}
