<?php

namespace App\Exports;

use App\Models\Product;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LowStockCsvExport
{
    public function download(): StreamedResponse
    {
        $threshold = (int) config('shop.low_stock_threshold', 5);

        return response()->streamDownload(function () use ($threshold) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['ID', 'Name', 'Price', 'Stock']);

            Product::where('stock_quantity', '<=', $threshold)
                ->orderBy('stock_quantity')
                ->cursor()
                ->each(function ($product) use ($handle) {
                    fputcsv($handle, [
                        $product->id,
                        $product->name,
                        number_format((float) $product->price, 2, '.', ''),
                        $product->stock_quantity,
                    ]);
                });

            fclose($handle);
        }, 'low-stock.csv');
    }
}
