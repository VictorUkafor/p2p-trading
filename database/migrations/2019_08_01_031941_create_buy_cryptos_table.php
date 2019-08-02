<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuyCryptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buy_cryptos', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('cryptocurrency');
            $table->string('payment_method');
            $table->string('method_details');
            $table->string('amount');
            $table->string('value');
            $table->string('status')->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('buy_cryptos', function($table) {
            $table->foreign('user_id')->references('id')
            ->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buy_cryptos');
    }
}
