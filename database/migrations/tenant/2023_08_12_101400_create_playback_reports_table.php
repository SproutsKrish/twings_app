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
        Schema::create('playback_reports', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('device_imei')->nullable()->comments('Stores the IMEI of the device, can be null');
            $table->double('latitude')->nullable()->comments('Latitude value, can be null');
            $table->double('longitude')->nullable()->comments('Longitude value, can be null');
            $table->double('speed')->default(0)->comments('Speed value with a default of 0');
            $table->double('odometer')->default(0)->comments('Odometer reading with a default of 0');
            $table->double('angle')->default(0)->comments('Angle value with a default of 0');
            $table->dateTime('device_datetime')->nullable()->comments('Date and time from the device, can be null');
            $table->tinyInteger('ignition_status')->default(0)->comments('Ignition status represented by a small integer, default value is 0');
            $table->tinyInteger('ac_status')->default(0)->comments('AC status represented by a small integer, default value is 0');
            $table->tinyInteger('packet_status')->default(0)->comments('Packet status represented by a small integer, default value is 0');
            $table->longText('packet_details')->nullable()->comments('Details about the packet, can be null');
            $table->timestamp('timestamp')->nullable()->comments('Timestamp column, can be null');
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
        Schema::dropIfExists('playback_reports');
    }
};
