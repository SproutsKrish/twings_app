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
        Schema::create('geofence_reports', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('assign_geofence_id')->comments('ID of the assigned geofence');
            $table->double('out_latitude')->nullable()->comments('Latitude of the exit point, can be null');
            $table->double('out_longitude')->nullable()->comments('Longitude of the exit point, can be null');
            $table->datetime('out_datetime')->comments('Date and time of exit');
            $table->double('in_latitude')->nullable()->comments('Latitude of the entry point, can be null');
            $table->double('in_longitude')->nullable()->comments('Longitude of the entry point, can be null');
            $table->datetime('in_datetime')->comments('Date and time of entry');
            $table->double('speed')->nullable()->comments('Speed value, can be null');
            $table->double('distance')->nullable()->comments('Distance value, can be null');
            $table->tinyInteger('ignition_status')->default(0)->comments('Ignition status represented by a small integer, default value is 0');
            $table->tinyInteger('ac_status')->default(0)->comments('AC status represented by a small integer, default value is 0');
            $table->bigInteger('vehicle_id')->nullable()->comments('Stores the ID of the associated vehicle, can be null');
            $table->bigInteger('client_id')->nullable()->comments('Stores the ID of the associated client, can be null');
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
        Schema::dropIfExists('geofence_reports');
    }
};
