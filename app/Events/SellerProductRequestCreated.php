<?php

namespace App\Events;

use App\Models\SellerProductRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SellerProductRequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public SellerProductRequest $sellerProductRequest)
    {
    }
}
