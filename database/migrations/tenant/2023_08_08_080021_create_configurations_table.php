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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id');
            $table->bigInteger('vehicle_id');
            $table->string('vehicle_name')->nullable();
            $table->string('device_imei')->nullable();
            $table->integer('parking_alert_time')->default(0);
            $table->integer('idle_alert_time')->default(0);
            $table->integer('speed_limit')->default(0);
            $table->integer('expected_mileage')->default(0);
            $table->integer('idle_rpm')->default(0);
            $table->integer('max_rpm')->default(0);
            $table->integer('temp_low')->default(0);
            $table->integer('temp_high')->default(0);
            $table->integer('fuel_fill_limit')->default(0);
            $table->integer('fuel_dip_limit')->default(0);
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
        Schema::dropIfExists('configurations');
    }
};
