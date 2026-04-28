<?php

namespace App\Mail;

use App\Models\SellerProductRequest;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SellerProductRequestNotification extends Mailable
{
    public function __construct(public SellerProductRequest $sellerProductRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'طلب منتجات جديد من البائع: ' . $this->sellerProductRequest->user->name,
            to: [Setting::adminEmail()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seller-product-request',
            with: [
                'sellerProductRequest' => $this->sellerProductRequest,
            ],
        );
    }
}
