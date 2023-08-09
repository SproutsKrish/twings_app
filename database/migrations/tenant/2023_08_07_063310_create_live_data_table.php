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
        Schema::create('live_data', function (Blueprint $table) {
            $table->id();
            $table->integer('vehicle_id')->nullable();
            $table->string('vehicle_name', 40)->nullable();
            $table->tinyInteger('vehicle_current_status')->nullable();
            $table->tinyInteger('vehicle_status')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->tinyInteger('ignition')->nullable();
            $table->tinyInteger('ac_status')->nullable();
            $table->float('speed')->default(0);
            $table->integer('angle')->nullable();
            $table->double('odometer')->default(0);
            $table->dateTime('device_updatedtime')->nullable();
            $table->float('temperature')->nullable();
            $table->float('device_battery_volt')->nullable();
            $table->float('vehicle_battery_volt')->nullable();
            $table->dateTime('last_ignition_on_time')->nullable();
            $table->dateTime('last_ignition_off_time')->nullable();
            $table->float('fuel_litre')->nullable();
            $table->tinyInteger('imobilizer_status')->nullable();
            $table->string('gpssignal')->nullable();
            $table->string('gsm_status')->nullable()->default(1);
            $table->decimal('rpm_value')->nullable();
            $table->tinyInteger('sec_engine_status')->default(0);
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
        //
    }
};
