<?php

namespace App\Listeners\Friendships;

use App\Events\Friendships\FriendshipAccepted;
use App\Notifications\FriendshipAcceptanceNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFriendshipAcceptanceNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FriendshipAccepted $event): void
    {
        $event->sender->notify(new FriendshipAcceptanceNotification($event));
    }
}
