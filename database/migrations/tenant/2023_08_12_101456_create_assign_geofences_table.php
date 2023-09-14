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
        Schema::create('assign_geofences', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->bigInteger('client_id')->nullable()->comments('Stores the ID of the associated client, can be null');
            $table->bigInteger('vehicle_id')->nullable()->comments('Stores the ID of the associated vehicle, can be null');
            $table->bigInteger('device_imei')->nullable();
            $table->bigInteger('geofence_id')->nullable()->comments('Stores the ID of the associated geofence, can be null');
            $table->enum('fence_type', ['Geofence', 'Vehiclefence'])->default('Geofence')
                ->comments('Type of fence: "Geofence" or "Vehiclefence", default is "Geofence"');
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
        Schema::dropIfExists('assign_geofences');
    }
};
