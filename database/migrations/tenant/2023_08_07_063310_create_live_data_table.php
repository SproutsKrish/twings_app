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
            $table->bigInteger('client_id');
            $table->bigInteger('vehicle_id')->nullable();
            $table->string('vehicle_name', 40)->nullable();
            $table->tinyInteger('vehicle_current_status')->nullable();
            $table->tinyInteger('vehicle_status')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->double('lattitute', 10, 6)->nullable();
            $table->double('longitute', 10, 6)->nullable();
            $table->double('altitude')->nullable();
            $table->tinyInteger('ignition')->nullable();
            $table->tinyInteger('ac_status')->nullable();
            $table->double('speed', 8, 2)->default(0.00);
            $table->integer('angle')->nullable();
            $table->double('odometer')->default(0);
            $table->datetime('device_updatedtime')->nullable();
            $table->double('temperature', 8, 2)->nullable();
            $table->double('device_battery_volt', 8, 2)->nullable();
            $table->double('vehicle_battery_volt', 8, 2)->nullable();
            $table->datetime('last_ignition_on_time')->nullable();
            $table->datetime('last_ignition_off_time')->nullable();
            $table->double('fuel_litre', 8, 2)->nullable();
            $table->tinyInteger('vehicle_sleep')->default(0);
            $table->tinyInteger('immobilizer_status')->nullable();
            $table->string('gpssignal', 255)->nullable();
            $table->string('gsm_status', 255)->default('1');
            $table->decimal('rpm_value', 8, 2)->nullable();
            $table->tinyInteger('sec_engine_status')->default(0);
            $table->tinyInteger('ignition_report_flag')->nullable();
            $table->datetime('ignition_report_datetime')->nullable();
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
