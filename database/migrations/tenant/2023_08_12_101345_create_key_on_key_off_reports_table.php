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
        Schema::create('key_on_key_off_reports', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('vehicle_id')->nullable()->comments('Stores the ID of the associated vehicle, can be null');
            $table->bigInteger('device_imei')->nullable()->comments('Stores the IMEI of the device, can be null');
            $table->double('start_latitude')->default(0)->comments('Latitude of starting point with a default value of 0');
            $table->double('start_longitude')->default(0)->comments('Longitude of starting point with a default value of 0');
            $table->dateTime('start_datetime')->nullable()->comments('Date and time when the journey started, can be null');
            $table->double('end_latitude')->default(0)->comments('Latitude of ending point with a default value of 0');
            $table->double('end_longitude')->default(0)->comments('Longitude of ending point with a default value of 0');
            $table->dateTime('end_datetime')->nullable()->comments('Date and time when the journey ended, can be null');
            $table->double('start_odometer')->default(0)->comments('Starting odometer reading with a default value of 0');
            $table->double('end_odometer')->default(0)->comments('Ending odometer reading with a default value of 0');
            $table->double('total_km')->default(0)->comments('Total kilometers traveled with a default value of 0');
            $table->string('start_fuel_litre', 45)->nullable()->comments('Amount of fuel at the start in liters, can be null');
            $table->string('end_fuel_litre', 45)->nullable()->comments('Amount of fuel at the end in liters, can be null');
            $table->string('vehicle_battery', 45)->nullable()->comments('Vehicle battery information, can be null');
            $table->string('device_battery', 45)->nullable()->comments('Device battery information, can be null');
            $table->longText('start_location')->nullable()->comments('Detailed starting location information, can be null');
            $table->longText('end_location')->nullable()->comments('Detailed ending location information, can be null');
            $table->tinyInteger('flag')->default(0)->comments('A small integer used as a flag, default value is 0');
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
        Schema::dropIfExists('key_on_key_off_reports');
    }
};
