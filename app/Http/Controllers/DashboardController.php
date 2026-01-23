<?php

namespace App\Http\Controllers;

use App\Exports\LowStockCsvExport;
use App\Exports\OrdersCsvExport;
use App\Services\Dashboard\DashboardChartService;
use App\Services\Dashboard\DashboardService;
use Inertia\Inertia;

class DashboardController
{
    public function index(
        DashboardService $service,
        DashboardChartService $charts
    ) {
        return Inertia::render('Dashboard/Index', [
            'stats' => $service->getStats(),
            'charts' => [
                'sales7days' => $charts->salesLast7Days(),
            ],
        ]);
    }

    public function exportOrders(OrdersCsvExport $export)
    {
        return $export->download();
    }

    public function exportLowStock(LowStockCsvExport $export)
    {
        return $export->download();
    }
}
