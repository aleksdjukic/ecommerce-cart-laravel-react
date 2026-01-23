<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use App\Services\Dashboard\DashboardChartService;
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
}
