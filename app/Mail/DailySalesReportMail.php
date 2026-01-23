<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailySalesReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $date,
        public readonly float $totalRevenue,
        public readonly int $ordersCount,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Sales Report'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-sales-report'
        );
    }
}
