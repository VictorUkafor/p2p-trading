<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSellCryptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_cryptos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wallet_id')->unsigned()->nullable();
            $table->string('cryptocurrency');
            $table->integer('bank_account_id');
            $table->integer('commission_id');
            $table->string('amount');
            $table->string('value');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('sell_cryptos', function($table) {
            $table->foreign('wallet_id')->references('id')
            ->on('wallets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sell_cryptos');
    }
}
