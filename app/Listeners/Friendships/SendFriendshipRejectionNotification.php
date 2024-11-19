<?php

namespace App\Listeners\Friendships;

use App\Events\Friendships\FriendshipRejected;
use App\Notifications\FriendshipRejectionNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendFriendshipRejectionNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(FriendshipRejected $event): void
    {
        $event->sender->notify(new FriendshipRejectionNotification($event));
    }
}
