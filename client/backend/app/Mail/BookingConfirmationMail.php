<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        protected Order $order,
        protected array $bookingDetails,
        protected string $qrCode
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[TravelVN] Xác nhận đặt tour/khách sạn - ' . $this->order->order_code,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-confirmation',
            with: [
                'order' => $this->order,
                'bookingDetails' => $this->bookingDetails,
                'qrCode' => $this->qrCode,
            ],
        );
    }

    public function attachments(): array
    {
        // Save QR code to temp file and attach it
        $tempFile = tempnam(sys_get_temp_dir(), 'qr_');
        $qrData = str_replace('data:image/png;base64,', '', $this->qrCode);
        file_put_contents($tempFile, base64_decode($qrData));

        return [
            Attachment::fromPath($tempFile)
                ->as('qr_code_' . $this->order->order_code . '.png')
                ->withMime('image/png'),
        ];
    }
}
