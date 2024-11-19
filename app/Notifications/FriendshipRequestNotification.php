<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class FriendshipRequestNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    protected $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toArray($notifiable)
    {
        return [
            'sender_id' => $this->event->sender->id,
            'sender_name' => $this->event->sender->name,
            'message' => "{$this->event->sender->name} has sent you a friendship request.",
            'type' => 'friendship_request',
            'friendship_id' => $this->event->friendship->id,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->event->receiver->id);
    }

    public function broadcastWith()
    {
        return [
            'message' => "{$this->event->sender->name} has sent you a friendship request.",
            'sender_id' => $this->event->sender->id,
            'sender_name' => $this->event->sender->name,
            'friendship_id' => $this->event->friendship->id,
        ];
    }
}
