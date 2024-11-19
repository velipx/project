<?php

namespace App\Listeners\Friendships;

use App\Events\Friendships\FriendshipRequested;
use App\Notifications\FriendshipRequestNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFriendshipRequestNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FriendshipRequested $event): void
    {
        $event->receiver->notify(new FriendshipRequestNotification($event));
    }
}
