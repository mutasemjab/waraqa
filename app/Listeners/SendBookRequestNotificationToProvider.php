<?php

namespace App\Listeners;

use App\Events\BookRequestCreated;
use App\Mail\BookRequestNotification;
use Illuminate\Support\Facades\Mail;

class SendBookRequestNotificationToProvider
{
    public function handle(BookRequestCreated $event)
    {
        $bookRequest = $event->bookRequest->load(['provider', 'items.product', 'user']);

        if ($bookRequest->provider && $bookRequest->provider->email) {
            try {
                Mail::send(new BookRequestNotification($bookRequest));
            } catch (\Exception $e) {
                \Log::error('Failed to send book request notification: ' . $e->getMessage());
            }
        }
    }
}
