<?php

namespace Tests\Feature;

use App\Mail\DailySalesReportMail;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DailySalesReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_sales_report_sends_mail(): void
    {
        Mail::fake();

        Order::factory()->create([
            'total_price' => 300,
            'created_at' => now()->subDay(),
        ]);

        $this->artisan('report:daily-sales')
            ->assertExitCode(0);

        Mail::assertSent(DailySalesReportMail::class);
    }
}
