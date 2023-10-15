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
        Schema::create('temp_distance_data', function (Blueprint $table) {
            $table->id();
            $table->integer('vehicle_id')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->double('start_odometer')->nullable();
            $table->double('end_odometer')->nullable();
            $table->double('distance')->nullable();
            $table->double('max_speed')->nullable();
            $table->double('avg_speed')->nullable();
            $table->double('min_speed')->nullable();
            $table->timestamp('created_time')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('temp_distance_data');
    }
};
