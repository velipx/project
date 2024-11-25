<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        // Fetch wallets with associated currencies for the authenticated user
        $wallets = [];
        if ($user) {
            $wallets = $user->wallets->map(function ($wallet) {
                return [
                    'id' => $wallet->id,
                    'name' => $wallet->name,
                    'balance' => $wallet->getBalanceForCurrency(),
                    'balanceUSD' => $wallet->convertBalanceToUSD(),
                    'currency' => $wallet->currencyRelation->only(['name', 'symbol', 'code', 'meta']),
                ];
            });
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'wallets' => $wallets,
                ] : null,
            ],
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error'),
                    'message' => $request->session()->get('message'),
                ];
            },
        ];
    }
}
