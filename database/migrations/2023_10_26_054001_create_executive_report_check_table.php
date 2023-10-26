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
            $table->bigInteger('user_id');
            $table->bigInteger('client_id');
            $table->tinyInteger('start_odometer')->default(0);
            $table->tinyInteger('end_odometer')->default(0);
            $table->tinyInteger('distance')->default(0);
            $table->tinyInteger('avg_speed')->default(0);
            $table->tinyInteger('min_speed')->default(0);
            $table->tinyInteger('max_speed')->default(0);
            $table->tinyInteger('rpm_milege_per_hour')->default(0);
            $table->tinyInteger('mileage_per_hour')->default(0);
            $table->tinyInteger('start_fuel')->default(0);
            $table->tinyInteger('end_fuel')->default(0);
            $table->tinyInteger('fuel_fill_litre')->default(0);
            $table->tinyInteger('fuel_dip_litre')->default(0);
            $table->tinyInteger('fuel_consumed_litre')->default(0);
            $table->tinyInteger('mileage')->default(0);
            $table->tinyInteger('start_engine_hour_meter')->default(0);
            $table->tinyInteger('end_engine_hour_meter')->default(0);
            $table->tinyInteger('total_engine_hour_meter')->default(0);
            $table->tinyInteger('parking_duration')->default(0);
            $table->tinyInteger('idle_duration')->default(0);
            $table->tinyInteger('moving_duration')->default(0);
            $table->tinyInteger('trip_duration')->default(0);
            $table->tinyInteger('ac_duration')->default(0);
            $table->tinyInteger('total_rpm_duration')->default(0);
            $table->tinyInteger('total_idle_rpm_duration')->default(0);
            $table->tinyInteger('total_normal_rpm_duration')->default(0);
            $table->tinyInteger('total_max_rpm_duration')->default(0);
            $table->tinyInteger('drum_left_rotation')->default(0);
            $table->tinyInteger('drum_right_rotation')->default(0);
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
        Schema::table('executive_report_check', function (Blueprint $table) {
            //
        });
    }
};
