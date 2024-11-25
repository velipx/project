<?php

namespace App\Models;

use Illuminate\Support\Facades\Http;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoAddress extends Model
{
    protected $fillable = ['wallet_id', 'address', 'private_key', 'is_default'];

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    // Set this address as default
    public function setDefault(): void
    {
        // Reset other addresses for the wallet
        $this->wallet->addresses()->update(['is_default' => false]);

        // Set this address as default
        $this->update(['is_default' => true]);
    }

    /**
     * @throws \Exception
     */
    public static function generateAddress($crypto)
    {
        // Send GET request to Node.js server to generate the address
        $response = Http::get('http://localhost:3000/api/addresses/generate/' . $crypto);

        // Check if the response is successful
        if ($response->successful()) {
            $data = $response->json();

            // Create a new CryptoAddress record and save to database
            return self::create([
                'wallet_id' => null,  // You can assign the wallet_id if available
                'address' => $data['address'],
                'private_key' => $data['privateKey'],
            ]);
        }

        // If the API call failed, throw an exception
        throw new \Exception("Failed to generate address for {$crypto}");
    }
}
