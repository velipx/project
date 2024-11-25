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
/*        $user = Auth()->user();
        $wallets = \App\Models\Wallet::with('currencyRelation')
            ->where('holder_type', get_class($user))
            ->where('holder_id', $user->id)
            ->get();

        foreach($wallets as $wallet)
        {
            echo $wallet->name . ' ' . $wallet->currencyRelation->name . ' ' . $wallet->convertBalanceToUSD() . '<br>';
        }*/

        return Inertia::render('Admin/Dashboard/Index');
    }
}
