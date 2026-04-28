<?php

namespace App\Mail;

use App\Models\BookRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BookRequestNotification extends Mailable
{
    public function __construct(public BookRequest $bookRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'طلب كتب جديد من ' . ($this->bookRequest->user->name ?? 'عميل'),
            to: [$this->bookRequest->provider->email],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.book-request',
            with: [
                'bookRequest' => $this->bookRequest,
            ],
        );
    }
}
