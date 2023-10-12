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
        Schema::create('hour_meter_reports', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('flag')->nullable();
            $table->double('s_lat', 15, 8)->nullable();
            $table->double('s_lng', 15, 8)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->bigInteger('vehicle_id')->nullable();
            $table->double('total_km', 15, 8)->default(0);
            $table->double('e_lat', 15, 8)->nullable();
            $table->double('e_lng', 15, 8)->nullable();
            $table->smallInteger('type_id')->nullable();
            $table->string('fuel_usage')->nullable();
            $table->string('fuel_filled')->nullable();
            $table->string('initial_ltr')->nullable();
            $table->string('end_ltr')->nullable();
            $table->binary('car_battery')->nullable();
            $table->binary('device_battery')->nullable();
            $table->double('start_odometer', 15, 8)->nullable();
            $table->double('end_odometer', 15, 8)->nullable();
            $table->double('start_hourmeter', 15, 8)->nullable();
            $table->double('end_hourmeter', 15, 8)->nullable();
            $table->double('real_start_odo', 15, 8)->nullable();
            $table->double('real_end_odo', 15, 8)->nullable();
            $table->longText('start_location')->nullable();
            $table->longText('end_location')->nullable();
            $table->integer('client_id')->nullable();
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
        Schema::dropIfExists('hour_meter_reports');
    }
};
