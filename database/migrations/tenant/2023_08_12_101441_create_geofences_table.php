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
        Schema::create('geofences', function (Blueprint $table) {
            $table->id()->comments('Auto-incrementing primary key');
            $table->string('location_short_name', 255)->comments('Short name or label for the location');
            $table->double('latitude')->comments('Latitude of the location');
            $table->double('longitude')->comments('Longitude of the location');
            $table->integer('circle_size')->nullable()->comments('Size of the circle associated with the location, can be null');
            $table->float('radius')->default(500)->comments('Radius of the circle in meters, default is 500 meters');
            $table->bigInteger('client_id')->comments('ID of the associated client');
            $table->tinyInteger('active_code')->default(1)->comments('Code indicating the active status of the location');
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
        Schema::dropIfExists('geofences');
    }
};
