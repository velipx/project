<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currencies = [
            ['name' => 'US Dollar', 'symbol' => '$', 'code' => 'USD', 'exchange_rate' => 1.0,
                'meta' => ['color' => '#00A86B', 'icon' => 'mdiCurrencyUsd']
            ],
            ['name' => 'Euro', 'symbol' => '€', 'code' => 'EUR', 'exchange_rate' => 0.9,
                'meta' => ['color' => '#000', 'icon' => 'mdiCurrencyEur']
            ],
            ['name' => 'Bitcoin', 'symbol' => '₿', 'code' => 'BTC', 'exchange_rate' => 60000.0,
                'meta' => ['color' => '#00A86B', 'icon' => 'mdiCurrencyBtc']
            ],
            ['name' => 'Ethereum', 'symbol' => 'Ξ', 'code' => 'ETH', 'exchange_rate' => 4000.0,
                'meta' => ['color' => '#00A86B', 'icon' => 'mdiEthereum']
            ],
        ];

        foreach ($currencies as $currency) {
            \App\Models\Currency::updateOrCreate(
                ['code' => $currency['code']],
                $currency
            );
        }
    }
}
