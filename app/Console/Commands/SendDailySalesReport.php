<?php

namespace App\Console\Commands;

use App\Services\Reports\DailySalesReportService;
use Illuminate\Console\Command;

class SendDailySalesReport extends Command
{
    protected $signature = 'report:daily-sales';
    protected $description = 'Send daily sales report to admin';

    public function handle(DailySalesReportService $service): int
    {
        $service->send();

        $this->info('Daily sales report sent.');

        return self::SUCCESS;
    }
}
