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
        Schema::create('consolidate_distance_report', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->bigInteger('vehicle_id');
            $table->bigInteger('device_imei')->nullable();
            $table->string('vehicle_register_number', 40)->nullable();
            $table->double('start_odometer');
            $table->double('end_odometer');
            $table->date('date')->nullable();
            $table->double('distance_km')->nullable();
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
        Schema::dropIfExists('consolidate_distance_report');
    }
};
