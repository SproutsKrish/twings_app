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
        Schema::create('fuel_escort_ble', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('running_no')->index();
            $table->double('latitude');
            $table->double('longtitude');
            $table->dateTime('modified_date')->nullable();
            $table->dateTime('created_date')->nullable();
            $table->tinyInteger('packet_type')->default(0);
            $table->double('raw_value')->default(0);
            $table->tinyInteger('speed')->default(0);
            $table->double('odometer')->nullable();
            $table->enum('ignition', ['1', '0'])->default('0');
            $table->string('keyword', 20)->nullable();
            $table->double('external_volt')->nullable();
            $table->double('battery_voltage')->nullable();
            $table->tinyInteger('device_ble_status')->nullable();
            $table->string('ble_fuel_temp_1', 20)->nullable();
            $table->double('ble_battery_1')->nullable();
            $table->string('ble_humidity_1', 20)->nullable();
            $table->double('ble_fuel_level_1')->nullable();
            $table->string('ble_luminosity_1', 20)->nullable();
            $table->string('ble_fuel_temp_2', 20)->nullable();
            $table->double('ble_battery_2')->nullable();
            $table->string('ble_humidity_2', 20)->nullable();
            $table->double('ble_fuel_level_2')->nullable();
            $table->string('ble_luminosity_2', 20)->nullable();
            $table->string('ble_fuel_temp_3', 20)->nullable();
            $table->double('ble_battery_3')->nullable();
            $table->string('ble_humidity_3', 20)->nullable();
            $table->double('ble_fuel_level_3')->nullable();
            $table->string('ble_luminosity_3', 20)->nullable();
            $table->string('ble_fuel_temp_4', 20)->nullable();
            $table->double('ble_battery_4')->nullable();
            $table->string('ble_humidity_4', 20)->nullable();
            $table->double('ble_fuel_level_4')->nullable();
            $table->string('ble_luminosity_4', 20)->nullable();
            $table->string('ble_custom_1', 20)->nullable();
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
        Schema::dropIfExists('fuel_escort_ble');
    }
};
