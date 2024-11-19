<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class FriendshipRejectionNotification extends Notification implements ShouldBroadcast
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
            'sender_name' => $this->event->receiver->name,
            'message' => "{$this->event->receiver->name} has rejected your friendship request.",
            'type' => 'friendship_rejection',
            'friendship_id' => $this->event->friendship->id,
        ];
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->event->sender->id);
    }

    public function broadcastWith()
    {
        return [
            'message' => "{$this->event->receiver->name} has rejected your friendship request.",
            'sender_id' => $this->event->sender->id,
            'sender_name' => $this->event->receiver->name,
            'friendship_id' => $this->event->friendship->id,
        ];
    }
}
