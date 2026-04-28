<?php

namespace App\Listeners;

use App\Events\SellerProductRequestCreated;
use App\Mail\SellerProductRequestNotification;
use Illuminate\Support\Facades\Mail;

class SendSellerProductRequestNotification
{
    public function handle(SellerProductRequestCreated $event)
    {
        $sellerProductRequest = $event->sellerProductRequest->load(['user', 'items.product']);

        try {
            Mail::send(new SellerProductRequestNotification($sellerProductRequest));
        } catch (\Exception $e) {
            \Log::error('Failed to send seller product request notification: ' . $e->getMessage());
        }
    }
}
