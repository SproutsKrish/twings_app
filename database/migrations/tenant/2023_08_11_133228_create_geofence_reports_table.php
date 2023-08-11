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
            $table->id();
            $table->integer('assign_geofence_id');
            $table->double('out_lat')->nullable();
            $table->double('out_lng')->nullable();
            $table->datetime('out_datetime');
            $table->double('in_lat')->nullable();
            $table->double('in_lng')->nullable();
            $table->datetime('in_datetime');
            $table->double('speed')->nullable();
            $table->smallInteger('ignition_status')->nullable();
            $table->smallInteger('ac_status')->nullable();
            $table->double('distance')->nullable();
            $table->integer('vehicle_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->smallInteger('location_status')->nullable();
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
