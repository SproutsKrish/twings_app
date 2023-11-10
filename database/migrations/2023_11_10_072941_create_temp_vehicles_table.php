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
        Schema::create('temp_vehicles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('device_imei')->nullable();
            $table->bigInteger('device_make_id')->nullable();
            $table->bigInteger('device_model_id')->nullable();
            $table->tinyInteger('status')->default(2);
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
        Schema::dropIfExists('temp_vehicles');
    }
};
