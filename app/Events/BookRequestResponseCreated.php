<?php

namespace App\Events;

use App\Models\BookRequestResponse;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookRequestResponseCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public BookRequestResponse $response)
    {
    }
}
