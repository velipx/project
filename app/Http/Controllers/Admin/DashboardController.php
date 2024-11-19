<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserNotification;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(): \Inertia\Response
    {
        $user = Auth()->user();


        Log::info('Handling FriendshipRequested event', [
            'test' => 'test'
        ]);

        $message = "Ovo je test notifikacija!";
        $url = route('admin.dashboard');
        $userId = $user->id;

        $user->notify(new UserNotification('teeeeee notifikacija 24!', $userId));

        return Inertia::render('Admin/Dashboard/Index');
    }
}
