<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWalletAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_addresses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('wallet_id')->unsigned()->nullable();
            $table->string('address');
            $table->string('coin');
            $table->string('balance');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('wallet_addresses', function($table) {
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
        Schema::dropIfExists('wallet_addresses');
    }
}
