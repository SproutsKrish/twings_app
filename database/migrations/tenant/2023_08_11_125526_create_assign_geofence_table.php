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
        Schema::create('assign_geofence', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->bigInteger('vehicle_id');
            $table->bigInteger('geofence_id')->nullable();
            $table->enum('fence_type', ['Geofence', 'Vehiclefence'])->default('Geofence');
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
        Schema::dropIfExists('assign_geofence');
    }
};
