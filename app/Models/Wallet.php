<?php

namespace App\Models;

use Bavix\Wallet\Models\Wallet as BavixWallet;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Wallet extends BavixWallet
{
    protected $fillable = [
        'holder_type',
        'holder_id',
        'name',
        'slug',
        'uuid',
        'description',
        'meta',
        'balance',
        'decimal_places',
        'currency_id',
    ];

    public function currencyRelation(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CryptoAddress::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(CryptoAddress::class)->where('is_default', true);
    }

    // Create a default wallet for the user and currency if it doesn't exist
    public static function createDefaultWallet(User $user, Currency $currency)
    {
        // Ensure a wallet exists for the user and currency
        $wallet = self::firstOrCreate([
            'holder_type' => User::class,
            'holder_id' => $user->id,
            'slug' => strtolower($currency->currency), // Ensure slug is unique per currency
        ], [
            'name' => $currency->currency,
            'balance' => 0,
            'currency_id' => $currency->id,
        ]);

        // Generate and set a default address if it doesn't exist
        if (!$wallet->defaultAddress()->exists()) {
            $cryptoAddress = CryptoAddress::generateAddress($currency->currency);
            $cryptoAddress->wallet_id = $wallet->id;
            $cryptoAddress->is_default = true;
            $cryptoAddress->save();
        }

        return $wallet;
    }

    // To fetch balance for a specific currency wallet
    public function getBalanceForCurrency(): string
    {
        return $this->balanceFloat;
    }

    public function convertBalanceToUSD(): string
    {
        $currentCurrency = $this->currencyRelation;

        if (!$currentCurrency) {
            throw new \Exception('Currency information not available for this wallet');
        }

        $usdCurrency = Currency::where('code', 'USD')->first();

        if (!$usdCurrency) {
            throw new \Exception('USD currency not found');
        }

        // Convert the balance to USD
        $usdBalance = $currentCurrency->convertTo($this->balanceFloat, $usdCurrency);

        // Format the balance to 2 decimal places
        $formattedBalance = number_format($usdBalance, 2);

        // Concatenate the USD currency symbol (assuming $ is the symbol for USD)
        $currencySymbol = $usdCurrency->symbol ?? '$'; // Default to $ if symbol is not set

        return $currencySymbol . $formattedBalance;
    }
}
