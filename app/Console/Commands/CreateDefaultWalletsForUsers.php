<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CreateDefaultWalletsForUsers extends Command
{
    protected $signature = 'wallets:create-default-address';
    protected $description = 'Generate addresses for wallets that do not have one';

    public function handle()
    {
        // Generate addresses for all wallets without an address
        $this->generateAddressesForUsers();

        return 0;
    }

    /**
     * Generate addresses for each user's wallet if it doesn't have one.
     */
    private function generateAddressesForUsers(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            // Iterate through each wallet of the user
            foreach ($user->wallets as $wallet) {
                // Ensure the wallet instance is of App\Models\Wallet
                $walletInstance = Wallet::find($wallet->id);
                if ($walletInstance && !$walletInstance->addresses()->exists()) {
                    $this->generateAddressForWallet($walletInstance);
                }
            }
            $this->info("Processed user: {$user->email}");
        }
    }

    /**
     * Generate an address for the user's wallet.
     */
    private function generateAddressForWallet(Wallet $wallet): void
    {
        // Get the JWT token from the environment variable
        $token = 'h1YuE0fasxXWYmVl5sxO1elPUlfYxruWVaaXfE';
        $this->info($token);
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

        $this->info("Address created for wallet ID {$wallet->id}");
    }
}
