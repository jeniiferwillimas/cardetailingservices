<?php

namespace Modules\Booking\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $customerName,
        public readonly string $orderReference,
        public readonly string $scheduledFor,
        public readonly string $address,
        public readonly string $state,
        public readonly ?string $vehicleInfo,
        public readonly array $serviceLines,
        public readonly float $total,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Booking Confirmed — '.config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'booking::emails.booking-confirmation',
        );
    }
}
