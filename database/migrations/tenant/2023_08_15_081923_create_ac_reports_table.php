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
        Schema::create('ac_reports', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('vehicle_id')->nullable()->comments('Stores the ID of the associated vehicle, can be null');
            $table->bigInteger('device_imei')->nullable()->comments('Stores the IMEI of the device, can be null');
            $table->double('start_latitude')->default(0)->comments('Latitude of starting point with a default value of 0');
            $table->double('start_longitude')->default(0)->comments('Longitude of starting point with a default value of 0');
            $table->dateTime('start_datetime')->nullable()->comments('Date and time when the journey started, can be null');
            $table->double('end_latitude')->default(0)->comments('Latitude of ending point with a default value of 0');
            $table->double('end_longitude')->default(0)->comments('Longitude of ending point with a default value of 0');
            $table->dateTime('end_datetime')->nullable()->comments('Date and time when the journey ended, can be null');
            $table->longText('start_location')->nullable()->comments('Detailed starting location information, can be null');
            $table->longText('end_location')->nullable()->comments('Detailed ending location information, can be null');
            $table->double('total_km')->nullable()->comment('Total kilometers');
            $table->bigInteger('type_id')->nullable()->comment('Type ID');
            $table->string('fuel_usage', 45)->nullable()->comment('Fuel usage');
            $table->string('fuel_filled', 45)->nullable()->comment('Fuel filled');
            $table->string('initial_ltr', 45)->nullable()->comment('Initial liters');
            $table->string('end_ltr', 45)->nullable()->comment('End liters');
            $table->double('start_odometer')->nullable()->comment('Start odometer');
            $table->double('end_odometer')->default(0)->comment('End odometer');
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
        Schema::dropIfExists('ac_reports');
    }
};
