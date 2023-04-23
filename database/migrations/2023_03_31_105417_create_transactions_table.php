<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            //room_id wallet_id transaction_type transaction_amount transaction_status
            $table->unsignedBigInteger('room_id')->nullable();
            //add hotel_id
            $table->unsignedBigInteger('hotel_id')->nullable();
            $table->string('wallet_id');
            //start date, end date, duration
            $table->datetime('check_in_date')->nullable();
            $table->datetime('check_out_date')->nullable();
            //transaction_id
            $table->string('transaction_id');
            
            $table->string('transaction_status')->nullable(); //0=success, 1=failed
            $table->string('transaction_amount')->nullable();

            //room password
            $table->string('room_password')->nullable();


            $table->foreign('room_id')->references('id')->on('rooms');
            $table->foreign('hotel_id')->references('id')->on('hotels');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
