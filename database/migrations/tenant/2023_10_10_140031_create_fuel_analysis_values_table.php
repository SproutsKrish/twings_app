<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuelAnalysisValuesTable extends Migration
{
    public function up()
    {
        Schema::create('fuel_analysis_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deviceimei')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->tinyInteger('ignition')->nullable();
            $table->tinyInteger('ac_status')->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->tinyInteger('speed')->nullable();
            $table->double('odometer')->nullable();
            $table->double('fuel_raw_value')->nullable();
            $table->double('raw_fuel_litre')->nullable();
            $table->double('smooth_fuel_litre')->nullable();
            $table->string('device_name', 100)->nullable();
            $table->tinyInteger('fuel_device_connect_status')->nullable();
            $table->decimal('fuel_temperature', 10, 2)->nullable();
            $table->datetime('device_datetime')->nullable();
            $table->text('packet_details')->nullable();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuel_analysis_values');
    }
}
