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
        Schema::create('distance_reports', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('client_id')->nullable()->comments('Stores the ID of the associated client, can be null');
            $table->bigInteger('vehicle_id')->nullable()->comments('Stores the ID of the associated vehicle, can be null');
            $table->bigInteger('device_imei')->nullable()->comments('Stores the IMEI of the device, can be null');
            $table->string('vehicle_register_number', 45)->nullable()->comments('Register number of the vehicle, can be null');
            $table->double('start_odometer')->nullable()->comments('Odometer reading at the start, can be null');
            $table->double('end_odometer')->nullable()->comments('Odometer reading at the end, can be null');
            $table->date('date')->nullable()->comments('Date of the record, can be null');
            $table->double('distance_km')->nullable()->comments('Distance traveled in kilometers, can be null');
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
        Schema::dropIfExists('distance_reports');
    }
};
