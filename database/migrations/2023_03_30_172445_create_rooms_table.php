<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            //room number, room type, room price, room status
            $table->string('room_number')->unique();
            $table->unsignedBigInteger('room_type_id');
            $table->string('room_status')->default("0");//0=available, 1=occupied, 2=reserved
            $table->foreign('room_type_id')->references('id')->on('room_types');

            //if he accepts the reservation, the room status will be changed to 1 
            $table->string("transaction_id")->nullable();
            //if room status is 1, then the transaction id will be filled
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
        Schema::dropIfExists('rooms');
    }
}
