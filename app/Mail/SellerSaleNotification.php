<?php

namespace App\Mail;

use App\Models\SellerSale;
use App\Models\Setting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class SellerSaleNotification extends Mailable
{
    public function __construct(public SellerSale $sellerSale)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'تسجيل مبيعات جديد من البائع: ' . $this->sellerSale->user->name,
            to: [Setting::adminEmail()],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.seller-sale',
            with: [
                'sellerSale' => $this->sellerSale,
            ],
        );
    }
}
