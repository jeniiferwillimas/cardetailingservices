<?php

namespace Modules\Support\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewChatMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly ?string $customerName,
        public readonly ?string $customerEmail,
        public readonly string $messageBody,
        public readonly string $conversationUuid,
    ) {}

    public function envelope(): Envelope
    {
        $from = $this->customerName ?? $this->customerEmail ?? 'A visitor';

        return new Envelope(
            subject: "New Chat Message from {$from} — ".config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'support::emails.new-chat-message',
        );
    }
}
