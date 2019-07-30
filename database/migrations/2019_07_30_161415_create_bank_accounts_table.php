<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('account_number');
            $table->string('account_name');
            $table->string('bank');
            $table->boolean('internet_banking')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('bank_accounts', function($table) {
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
        Schema::dropIfExists('bank_accounts');
    }
}
