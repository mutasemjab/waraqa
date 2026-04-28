<?php

namespace App\Mail;

use App\Models\BookRequest;
use App\Models\BookRequestResponse;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class BookRequestResponseNotification extends Mailable
{
    public function __construct(
        public BookRequestResponse $response,
        public BookRequest $bookRequest
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تم الرد على طلب الشراء',
            to: [Setting::adminEmail()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.book-request-response',
            with: [
                'response' => $this->response,
                'bookRequest' => $this->bookRequest,
            ],
        );
    }
}
