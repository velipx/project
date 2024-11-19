<?php

namespace App\Notifications;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Notifications\Notification;

class UserNotification extends Notification implements ShouldBroadcast
{
    use Queueable;

    public $message;
    public $userId;

    /**
     * Create a new notification instance.
     */
    public function __construct($message = null, $userId)
    {
        $this->message = $message ?? 'New Notification';
        $this->userId = $userId;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'type' => 'NotificationType'
        ];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }
}
