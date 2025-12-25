<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $invoicePath
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Hóa Đơn Thanh Toán - Đơn Hàng {$this->order->order_code}"
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice',
            with: [
                'order' => $this->order,
                'customer' => $this->order->user,
            ]
        );
    }

    public function attachments(): array
    {
        if (!$this->invoicePath || !file_exists($this->invoicePath)) {
            return [];
        }
        
        return [
            Attachment::fromPath($this->invoicePath)
                ->as("hoa_don_{$this->order->order_code}.pdf")
                ->withMime('application/pdf'),
        ];
    }
}
