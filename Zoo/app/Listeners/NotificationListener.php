<?php

namespace App\Listeners;

use App\Events\MessageNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MessageNotification $event): void
    {
        $notificationData = $event->notificationData;
        $unreadCount = $event->unreadCount;
        echo $unreadCount;
    }
}
