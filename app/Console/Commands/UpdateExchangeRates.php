<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Currency;

class UpdateExchangeRates extends Command
{
    protected $signature = 'exchange:update-rates';
    protected $description = 'Update currency exchange rates';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Currency::updateExchangeRates();
        $this->info('Exchange rates updated successfully.');
    }
}
