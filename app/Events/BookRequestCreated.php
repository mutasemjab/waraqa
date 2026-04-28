<?php

namespace App\Events;

use App\Models\BookRequest;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookRequestCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public BookRequest $bookRequest)
    {
    }
}
