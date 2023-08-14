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
        Schema::create('tripplan_reports', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('client_id')->nullable()->comments('Stores the ID of the associated client, can be null');
            $table->bigInteger('device_imei')->nullable()->comments('Stores the IMEI of the associated device, can be null');
            $table->bigInteger('vehicle_id')->nullable()->comments('Stores the ID of the associated vehicle, can be null');
            $table->string('vehicle_name')->nullable()->comments('Name of the associated vehicle, can be null');
            $table->longText('start_location')->nullable()->comments('Detailed starting location information, can be null');
            $table->longText('end_location')->nullable()->comments('Detailed ending location information, can be null');
            $table->string('poc_number')->nullable()->comments('Point of Contact (POC) number, can be null');
            $table->bigInteger('route_id')->nullable()->comments('Stores the ID of the associated route, can be null');
            $table->string('route_name')->nullable()->comments('Name of the associated route, can be null');
            $table->bigInteger('start_geofence_id')->nullable()->comments('Stores the ID of the starting geofence, can be null');
            $table->bigInteger('end_geofence_id')->nullable()->comments('Stores the ID of the ending geofence, can be null');
            $table->tinyInteger('geofence_status')->nullable()->comments('Geofence status represented by a small integer, can be null');
            $table->dateTime('trip_date')->nullable()->comments('Date of the trip, can be null');
            $table->string('trip_type')->nullable()->comments('Type of the trip, can be null');
            $table->string('parking_duration')->nullable()->comments('Duration of parking, can be null');
            $table->string('idle_duration')->nullable()->comments('Duration of idle time, can be null');
            $table->double('start_odometer')->default(0)->comments('Odometer reading at the start with a default value of 0');
            $table->double('end_odometer')->default(0)->comments('Odometer reading at the end with a default value of 0');
            $table->double('distance_km')->default(0)->comments('Distance traveled in kilometers with a default value of 0');
            $table->double('start_latitude')->default(0)->comments('Latitude of starting point with a default value of 0');
            $table->double('start_longitude')->default(0)->comments('Longitude of starting point with a default value of 0');
            $table->double('end_latitude')->default(0)->comments('Latitude of ending point with a default value of 0');
            $table->double('end_longitude')->default(0)->comments('Longitude of ending point with a default value of 0');
            $table->tinyInteger('flag')->default(0)->comments('A small integer used as a flag, default value is 0');
            $table->tinyInteger('status')->nullable()->comments('Status represented by a small integer, can be null');
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
        Schema::dropIfExists('tripplan_reports');
    }
};
