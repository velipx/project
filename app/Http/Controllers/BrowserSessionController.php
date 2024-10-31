<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;

class BrowserSessionController extends Controller
{
    public function index()
    {
        $sessions = DB::table('sessions')
            ->where('user_id', Auth::id())
            ->orderBy('last_activity', 'desc')
            ->get();

        return view('sessions.index', [
            'sessions' => $sessions,
        ]);
    }

    public function destroy(Request $request, $sessionId)
    {
        $request->user()->tokens()->where('id', $sessionId)->delete();

        return back()->with('status', 'Session deleted successfully.');
    }
}
