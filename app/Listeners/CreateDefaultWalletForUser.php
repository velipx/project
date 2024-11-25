<?php

namespace App\Listeners;

use App\Models\Currency;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Http;

class CreateDefaultWalletForUser
{
    /**
     * @throws \Exception
     */
    public function handle(Registered $event): void
    {
        $user = $event->user;

        if ($user->wallets()->exists()) {
            return;
        }

        $defaultCurrency = Currency::where('code', 'BTC')->first();

        if (!$defaultCurrency) {
            throw new \Exception("Default currency not found. Please seed currencies.");
        }

        $wallet = $user->createWallet([
            'holder_type' => User::class,
            'holder_id' => $user->id,
            'name' => "{$defaultCurrency->name} Wallet",
            'slug' => strtolower($defaultCurrency->code),
            'currency_id' => $defaultCurrency->id,
        ]);

        $token = 'h1YuE0fasxXWYmVl5sxO1elPUlfYxruWVaaXfE';
        // Call Node.js API to generate address with authorization token
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token
        ])->get('http://localhost:3000/api/addresses/generate/BTC'); // Assuming Node.js API is serving on this port

        if ($response->failed()) {
            $this->error("Failed to generate address for wallet ID {$wallet->id}. Response: " . $response->body());
            return;
        }

        // Assume Node.js returns JSON with address and private key
        $data = $response->json();

        // Create CryptoAddress and associate it with the Wallet
        $wallet->addresses()->create([
            'address' => $data['address'],
            'private_key' => $data['privateKey'],
            'is_default' => true,
        ]);
    }
}
