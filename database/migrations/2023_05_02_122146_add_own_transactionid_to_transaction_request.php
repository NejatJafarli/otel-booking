<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnTransactionidToTransactionRequest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_requests', function (Blueprint $table) {
            //
            //add strin 
            $table->string('own_transaction_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transaction_requests', function (Blueprint $table) {
            //
            //drop
            $table->dropColumn('own_transaction_id');
        });
    }
}
