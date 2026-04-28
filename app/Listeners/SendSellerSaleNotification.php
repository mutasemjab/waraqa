<?php

namespace App\Listeners;

use App\Events\SellerSaleCreated;
use App\Mail\SellerSaleNotification;
use App\Models\Setting;
use Illuminate\Support\Facades\Mail;

class SendSellerSaleNotification
{
    public function handle(SellerSaleCreated $event)
    {
        $sellerSale = $event->sellerSale->load(['user', 'items.product']);

        try {
            Mail::send(new SellerSaleNotification($sellerSale));
        } catch (\Exception $e) {
            \Log::error('Failed to send seller sale notification: ' . $e->getMessage());
        }
    }
}
