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
        Schema::create('temperature_reports', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->bigInteger('device_imei')->nullable()->comment('Device IMEI');
            $table->double('latitude')->nullable()->comment('Latitude');
            $table->double('longitude')->nullable()->comment('Longitude');
            $table->float('angle')->nullable()->comment('Angle');
            $table->float('speed')->nullable()->comment('Speed');
            $table->tinyInteger('ignition_status')->nullable()->comment('Ignition status');
            $table->tinyInteger('ac_status')->nullable()->comment('AC status');
            $table->double('odometer')->nullable()->comment('Odometer');
            $table->float('temp_status1')->nullable()->comment('Temperature status 1');
            $table->float('humidity1')->nullable()->comment('Humidity 1');
            $table->float('temp_status2')->nullable()->comment('Temperature status 2');
            $table->float('humidity2')->nullable()->comment('Humidity 2');
            $table->float('temp_status3')->nullable()->comment('Temperature status 3');
            $table->float('humidity3')->nullable()->comment('Humidity 3');
            $table->float('temp_status4')->nullable()->comment('Temperature status 4');
            $table->float('humidity4')->nullable()->comment('Humidity 4');
            $table->float('temp_status5')->nullable()->comment('Temperature status 5');
            $table->float('humidity5')->nullable()->comment('Humidity 5');
            $table->integer('packet_type')->nullable()->comment('Packet type');
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
        Schema::dropIfExists('temperature_reports');
    }
};
