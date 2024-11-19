<?php

namespace App\Events\Friendships;

use App\Models\Friendship;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendshipRejected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $receiver;
    public $friendship;

    public function __construct(Friendship $friendship)
    {
        $this->sender = $friendship->sender;
        $this->receiver = $friendship->receiver;
        $this->friendship = $friendship;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->sender->id);
    }

    public function broadcastAs(): string
    {
        return 'friendship.rejected';
    }
}
