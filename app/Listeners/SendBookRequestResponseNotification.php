<?php

namespace App\Listeners;

use App\Events\BookRequestResponseCreated;
use App\Mail\BookRequestResponseNotification;
use Illuminate\Support\Facades\Mail;

class SendBookRequestResponseNotification
{
    public function handle(BookRequestResponseCreated $event)
    {
        $response = $event->response->load(['bookRequestItem.bookRequest', 'provider']);
        $bookRequest = $response->bookRequestItem->bookRequest;

        try {
            Mail::send(new BookRequestResponseNotification($response, $bookRequest));
        } catch (\Exception $e) {
            \Log::error('Failed to send book request response notification: ' . $e->getMessage());
        }
    }
}
