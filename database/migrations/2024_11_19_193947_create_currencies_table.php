<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Naziv valute, npr. "US Dollar"
            $table->string('symbol')->unique(); // Simbol valute, npr. "$", "€", "₿"
            $table->string('code')->unique(); // ISO kod valute, npr. "USD", "EUR", "BTC"
            $table->decimal('exchange_rate', 15, 8); // Kurs prema osnovnoj valuti
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
