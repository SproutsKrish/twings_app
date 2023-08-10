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
        Schema::create('geofence', function (Blueprint $table) {
            $table->id();
            $table->string('location_short_name', 255);
            $table->double('lat');
            $table->double('lng');
            $table->integer('circle_size');
            $table->float('radius')->default(500);
            $table->bigInteger('client_id');
            $table->integer('active_code');
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
        Schema::dropIfExists('geofence');
    }
};
