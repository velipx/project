<?php

namespace App\Events\Friendships;

use App\Models\Friendship;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FriendshipRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sender;
    public $receiver;
    public $friendship;

    public function __construct(User $sender, User $receiver, Friendship $friendship)
    {
        $this->sender = $sender;
        $this->receiver = $receiver;
        $this->friendship = $friendship;
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->receiver->id);
    }

    public function broadcastAs(): string
    {
        return 'friendship.requested';
    }
}
