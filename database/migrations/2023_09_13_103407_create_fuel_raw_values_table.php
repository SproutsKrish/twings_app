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
        Schema::create('fuel_raw_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('device_id')->nullable();
            $table->double('deviceimei')->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->float('speed')->default(0);
            $table->tinyInteger('ignition')->default(0);
            $table->double('odometer')->nullable();
            $table->double('raw_value')->default(0);
            $table->datetime('device_datetime')->nullable();
            $table->timestamp('server_time')->useCurrent();
            $table->text('packet');
            $table->tinyInteger('packet_status')->default(0);
            $table->string('device_name', 50)->nullable();
            $table->double('device_battery_volt')->nullable();
            $table->double('vehicle_battery_volt')->nullable();
            $table->tinyInteger('fuel_device_status')->nullable();
            $table->double('fuel_temp')->nullable();
            $table->double('fuel_device_battery_volt')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('fuel_raw_values');
    }
};
