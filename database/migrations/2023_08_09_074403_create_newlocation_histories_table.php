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
        Schema::create('new_location_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deviceimei', 20)->comment("Device IMEI");
            // $table->double("lattitute")->comment("Lattitute");
            // $table->double("longitute")->comment("Longtitude");
            // $table->tinyInteger("ignition",4)->default(0)->comment("Ignition Status");
            // $table->tinyInteger("ac_status")->default(0)->comment("Lattitute");
            // $table->float("speed")->default(0)->comment("Vehicle Speed");
            // $table->smallInteger("angle",6)->default(0)->comment("Angle");
            // $table->dateTime("device_datetime")->comment("Device DateTime");
            // $table->double("distance_with_odometer")->default(0)->comment("Odometer with device");
            // $table->double("distance_without_odometer")->default(0)->comment("Odometer without device");
            // $table->float("device_battery_volt")->nullable()->comment("Device Battery Voltage");
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
        Schema::dropIfExists('newlocation_histories');
    }
};
