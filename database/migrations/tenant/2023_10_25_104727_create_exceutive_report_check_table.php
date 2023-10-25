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
        Schema::create('exceutive_report_check', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id');
            $table->bigInteger('client_id');
            $table->tinyInteger('start_odometer')->nullable();
            $table->tinyInteger('end_odometer')->nullable();
            $table->tinyInteger('distance')->nullable();
            $table->tinyInteger('avg_speed')->nullable();
            $table->tinyInteger('min_speed')->nullable();
            $table->tinyInteger('max_speed')->nullable();
            $table->tinyInteger('rpm_milege_per_hour')->nullable();
            $table->tinyInteger('mileage_per_hour')->nullable();
            $table->tinyInteger('start_fuel')->nullable();
            $table->tinyInteger('end_fuel')->nullable();
            $table->tinyInteger('fuel_fill_litre')->nullable();
            $table->tinyInteger('fuel_dip_litre')->nullable();
            $table->tinyInteger('fuel_consumed_litre')->nullable();
            $table->tinyInteger('mileage')->nullable();
            $table->tinyInteger('start_engine_hour_meter')->nullable();
            $table->tinyInteger('end_engine_hour_meter')->nullable();
            $table->tinyInteger('total_engine_hour_meter')->nullable();
            $table->tinyInteger('parking_duration')->nullable();
            $table->tinyInteger('idle_duration')->nullable();
            $table->tinyInteger('moving_duration')->nullable();
            $table->tinyInteger('trip_duration')->nullable();
            $table->tinyInteger('ac_duration')->nullable();
            $table->tinyInteger('total_rpm_duration')->nullable();
            $table->tinyInteger('total_idle_rpm_duration')->nullable();
            $table->tinyInteger('total_normal_rpm_duration')->nullable();
            $table->tinyInteger('total_max_rpm_duration')->nullable();
            $table->tinyInteger('drum_left_rotation')->nullable();
            $table->tinyInteger('drum_right_rotation')->nullable();
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
        Schema::dropIfExists('exceutive_report_check');
    }
};
