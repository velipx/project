<?php

namespace App\Models;

use Bavix\Wallet\Models\Wallet;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $fillable = ['name', 'symbol', 'code', 'exchange_rate', 'meta'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function wallets(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Wallet::class);
    }

    public function convertTo($amount, Currency $targetCurrency): float|int
    {
        $exchangeRate = $this->exchange_rate; // Exchange rate of current currency to base currency (USD for example)
        $targetExchangeRate = $targetCurrency->exchange_rate; // Exchange rate of target currency to base currency

        if ($exchangeRate == 0 || $targetExchangeRate == 0) {
            throw new \Exception('Invalid exchange rate');
        }

        // Convert amount from current currency to target currency
        return ($amount * $exchangeRate) / $targetExchangeRate;
    }

    public static function updateExchangeRates()
    {
        $client = new Client();
        $apiKey = '78cf7f80-1b3f-4edd-ada4-1e2a22e64229'; // Replace with your actual API key
        $symbols = ['USD', 'EUR', 'BTC', 'ETH']; // Add or modify currencies as needed

        try {
            // Join symbols by commas
            $symbolList = implode(',', $symbols);

            // Fetch quotes for specified symbols in USD
            $response = $client->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/quotes/latest', [
                'query' => [
                    'symbol' => $symbolList,
                    'convert' => 'USD', // Base conversion to USD
                ],
                'headers' => [
                    'X-CMC_PRO_API_KEY' => $apiKey,
                ],
            ]);

            $data = json_decode($response->getBody(), true)['data'];

            foreach ($symbols as $symbol) {
                // Ignore USD itself if included in symbols
                if ($symbol === 'USD') continue;

                $currencyModel = self::where('code', $symbol)->first();

                if ($currencyModel && isset($data[$symbol])) {
                    $exchangeRateUSD = $data[$symbol]['quote']['USD']['price'];
                    \Log::info($exchangeRateUSD . ' - ' . $symbol);
                    $currencyModel->update([
                        'exchange_rate' => $exchangeRateUSD,
                    ]);
                }
            }

            \Log::info('Exchange rates updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to update exchange rates: ' . $e->getMessage());
        }
    }
}
