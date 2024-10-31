<?php
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;

Route::get('/api/user/{user}', function ($username) {
    $cacheKey = 'user_' . $username;

    if (Cache::has($cacheKey)) {
        $userData = Cache::get($cacheKey);
    } else {
        $userData = User::with('roles')->where('username', $username)->first();
        Cache::put($cacheKey, $userData, 300);
    }

    return response()->json($userData);
});
