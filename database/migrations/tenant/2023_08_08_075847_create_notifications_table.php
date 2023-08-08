<?php

use App\Models\Vehicle;
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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id');
            $table->tinyInteger('ac_on')->default(0);
            $table->tinyInteger('ac_off')->default(0);
            $table->tinyInteger('ignition_on')->default(0);
            $table->tinyInteger('ignition_off')->default(0);
            $table->tinyInteger('speed_alert')->default(0);
            $table->tinyInteger('route_deviation')->default(0);
            $table->tinyInteger('temperature_alert')->default(0);
            $table->tinyInteger('sos_alert')->default(0);
            $table->tinyInteger('geofence_in_circle')->default(0);
            $table->tinyInteger('geofence_out_circle')->default(0);
            $table->tinyInteger('acceleration')->default(0);
            $table->tinyInteger('braking')->default(0);
            $table->tinyInteger('cornering')->default(0);
            $table->tinyInteger('speed_breaker_bump')->default(0);
            $table->tinyInteger('accident')->default(0);
            $table->tinyInteger('fuel_dip')->default(0);
            $table->tinyInteger('fuel_fill')->default(0);
            $table->tinyInteger('power_off')->default(0);
            $table->tinyInteger('hub_in_circle')->default(0);
            $table->tinyInteger('hub_out_circle')->default(0);
            $table->tinyInteger('low_battery')->default(0);
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
        Schema::dropIfExists('notifications');
    }
};
