<?php

namespace App\Http\Controllers;

use App\Events\Friendships\FriendshipRequested;
use App\Events\Friendships\FriendshipAccepted;
use App\Events\Friendships\FriendshipRejected;
use App\Models\Friendship;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class FriendshipController extends Controller
{
    public function sendRequest(Request $request, User $receiver): \Illuminate\Http\RedirectResponse
    {
        $sender = $request->user();

        if ($this->friendshipExists($sender->id, $receiver->id)) {
            return redirect()->back()->withErrors('Friendship request already exists.');
        }

        $friendship = Friendship::create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'status' => 'pending',
        ]);

        event(new FriendshipRequested($sender, $receiver, $friendship));

        return redirect()->back()->with('success', 'Friendship request sent.');
    }

    public function acceptRequest(Request $request, Friendship $friendship): JsonResponse
    {
        return $this->handleFriendshipUpdate($request->user(), $friendship, 'accepted', new FriendshipAccepted($friendship));
    }

    public function rejectRequest(Request $request, Friendship $friendship): JsonResponse
    {
        return $this->handleFriendshipUpdate($request->user(), $friendship, 'rejected', new FriendshipRejected($friendship));
    }

    protected function handleFriendshipUpdate(User $user, Friendship $friendship, string $status, $event): JsonResponse
    {
        if (!$this->isReceiver($user->id, $friendship)) {
            return response()->json(['error' => 'You are not authorized to perform this action.'], 403);
        }

        $friendship->update(['status' => $status]);
        event($event);

        $notification = $this->markNotificationAsRead($friendship->id, $user->id);

        return response()->json(['message' => "Friendship request {$status}.", 'notification' => $notification]);
    }

    protected function markNotificationAsRead($friendshipId, $userId): ?DatabaseNotification
    {
        $friendshipId = (string) $friendshipId;
        // Using jsonb_extract_path_text function for PostgreSQL
        $notification = DatabaseNotification::whereRaw("jsonb_extract_path_text(data::jsonb, 'friendship_id') = ? AND jsonb_extract_path_text(data::jsonb, 'type') = 'friendship_request'", [$friendshipId])
            ->where('notifiable_id', $userId)
            ->first();

        $notification?->markAsRead();

        return $notification;
    }

    protected function friendshipExists($userId1, $userId2): bool
    {
        return Friendship::where(function($query) use ($userId1, $userId2) {
            $query->where('sender_id', $userId1)->orWhere('receiver_id', $userId1);
        })->where(function($query) use ($userId2) {
            $query->where('sender_id', $userId2)->orWhere('receiver_id', $userId2);
        })->exists();
    }

    protected function isReceiver($userId, Friendship $friendship): bool
    {
        return $friendship->receiver_id === $userId;
    }
}
