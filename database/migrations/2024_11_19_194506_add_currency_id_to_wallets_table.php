<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->foreignId('currency_id')->nullable()->constrained()->onDelete('set null');

            // Use holder_id and holder_type for the unique constraint
            $table->unique(['holder_id', 'holder_type', 'currency_id'], 'wallets_holder_currency_unique');
        });
    }

    public function down()
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique('wallets_holder_currency_unique');
            $table->dropColumn('currency_id');
        });
    }
};
