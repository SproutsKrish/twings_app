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
        Schema::create('routedeviation_reports', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('client_id')->nullable()->comments('Stores the ID of the associated client, can be null');
            $table->bigInteger('route_id')->nullable()->comments('Stores the ID of the associated route, can be null');
            $table->string('route_name')->nullable()->comments('Name of the route, can be null');
            $table->string('device_imei')->nullable()->comments('IMEI of the associated device, can be null');
            $table->string('vehicle_name')->nullable()->comments('Name of the associated vehicle, can be null');
            $table->string('route_deviate_out_time')->nullable()->comments('Deviation out time for the route, can be null');
            $table->string('route_out_location')->nullable()->comments('Location of route out point, can be null');
            $table->string('route_out_latitude')->nullable()->comments('Latitude of route out point, can be null');
            $table->string('route_out_longitude')->nullable()->comments('Longitude of route out point, can be null');
            $table->string('route_deviate_in_time')->nullable()->comments('Deviation in time for the route, can be null');
            $table->string('route_in_location')->nullable()->comments('Location of route in point, can be null');
            $table->string('route_in_latitude')->nullable()->comments('Latitude of route in point, can be null');
            $table->string('route_in_longitude')->nullable()->comments('Longitude of route in point, can be null');
            $table->tinyInteger('location_status')->nullable()->comments('Status of location, represented by a small integer, can be null');
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
        Schema::dropIfExists('routedeviation_reports');
    }
};
