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
            $table->bigInteger('deviceimei')->nullable();
            $table->double('lattitute', 10, 6)->nullable();
            $table->double('longitute', 10, 6)->nullable();
            $table->tinyInteger('ignition')->default(0);
            $table->tinyInteger('ac_status')->default(0);
            $table->double('speed')->default(0);
            $table->smallInteger('angle')->default(0);
            $table->datetime('device_datetime')->nullable();
            $table->double('distance_with_odometer')->default(0);
            $table->double('distance_without_odometer')->default(0);
            $table->double('device_battery_volt', 8, 2)->nullable();
            $table->double('vehicle_battery_volt', 8, 2)->nullable();
            $table->string('device_battery_percent', 10)->nullable();
            $table->string('device_name', 15)->nullable();
            $table->string('gpssignal', 15)->nullable();
            $table->smallInteger('gsm_status')->default(0);
            $table->tinyInteger('gps_statelite')->default(0);
            $table->smallInteger('altitude')->default(0);
            $table->string('cell_id', 30)->nullable();
            $table->tinyInteger('packet_status')->default(0);
            $table->tinyInteger('power_status')->default(1);
            $table->longText('packet')->nullable();
            $table->tinyInteger('vehicle_sleep')->default(0);
            $table->tinyInteger('sec_engine_status')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('io_state')->default(0);
            $table->datetime('server_time')->nullable();
            // Remove the timestamps() method if not needed
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
        Schema::dropIfExists('new_location_history');
    }
};
