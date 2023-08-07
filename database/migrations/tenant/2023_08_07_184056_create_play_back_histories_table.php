<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('play_back_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deviceimei')->nullable();
            $table->double('lattitute');
            $table->double('longitute');
            $table->double('speed')->default(0);
            $table->double('odometer')->default(0);
            $table->double('angle')->default(0);
            $table->datetime('device_datetime')->nullable();
            $table->tinyInteger('ignition')->default(0);
            $table->tinyInteger('ac_status')->default(0);
            $table->tinyInteger('packet_status')->nullable();
            $table->longText('packet_details')->nullable();
            $table->smallInteger('client_id')->default(0);
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
        Schema::dropIfExists('play_back_histories');
    }
};
