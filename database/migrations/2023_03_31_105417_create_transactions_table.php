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
            $table->unsignedBigInteger('room_id');
            $table->string('wallet_id');
            //start date, end date, duration
            $table->date('check_in_date');
            $table->date('check_out_date');
            //transaction_id\
            $table->string('transaction_id');
            
            $table->string('transaction_payment_method'); //0=paypal, 1=credit card
            $table->string('transaction_booking_status');// 0 =pending, 1=accepted, 2=declined
            $table->string('transaction_status'); //0=success, 1=failed
            $table->string('transaction_amount');
            $table->foreign('room_id')->references('id')->on('rooms');
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