<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Product $product
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Low stock alert: {$this->product->name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.low-stock',
            with: [
                'product' => $this->product,
                'threshold' => config('shop.low_stock_threshold'),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
