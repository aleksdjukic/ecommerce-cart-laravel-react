<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Inertia\Inertia;

class DashboardController
{
    public function index(DashboardService $service)
    {
        return Inertia::render('Dashboard/Index', [
            'stats' => $service->getStats(),
        ]);
    }
}
