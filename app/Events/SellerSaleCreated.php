<?php

namespace App\Events;

use App\Models\SellerSale;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerSaleCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SellerSale $sellerSale)
    {
    }
}
