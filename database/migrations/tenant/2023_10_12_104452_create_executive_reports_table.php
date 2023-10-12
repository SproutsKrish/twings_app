<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExecutiveReportsTable extends Migration
{
    public function up()
    {
        Schema::create('executive_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->date('report_date')->nullable();
            $table->string('vehicle_name', 70)->nullable();
            $table->double('start_odometer')->nullable();
            $table->double('end_odometer')->nullable();
            $table->double('distance')->nullable();
            $table->smallInteger('avg_speed')->nullable();
            $table->smallInteger('min_speed')->nullable();
            $table->smallInteger('max_speed')->nullable();
            $table->double('start_engine_hour_meter')->nullable();
            $table->double('end_engine_hour_meter')->nullable();
            $table->integer('parking_duration')->nullable();
            $table->integer('idle_duration')->nullable();
            $table->integer('moving_duration')->nullable();
            $table->integer('trip_duration')->nullable();
            $table->integer('ac_duration')->nullable();
            $table->integer('total_rpm_duration')->nullable();
            $table->integer('total_idle_rpm_duration')->nullable();
            $table->integer('total_normal_rpm_duration')->nullable();
            $table->integer('total_max_rpm_duration')->nullable();
            $table->double('rpm_milege_per_hour')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('executive_reports');
    }
}
