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
        Schema::create('keyoff_keyon_reports', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('flag')->nullable();
            $table->double('start_latitude')->default(0);
            $table->double('start_longitude')->default(0);
            $table->dateTime('start_datetime')->nullable();
            $table->dateTime('end_datetime')->nullable();
            $table->bigInteger('device_imei')->nullable();
            $table->bigInteger('vehicle_id')->nullable();
            $table->double('total_km')->default(0);
            $table->double('end_latitude')->default(0);
            $table->double('end_longitude')->default(0);
            $table->integer('type_id')->nullable();
            $table->string('fuel_usage', 45)->nullable();
            $table->string('fuel_filled', 45)->nullable();
            $table->string('initial_ltr', 45)->nullable();
            $table->string('end_ltr', 45)->nullable();
            $table->string('car_battery', 45)->nullable();
            $table->string('device_battery', 45)->nullable();
            $table->double('start_odometer')->default(0);
            $table->double('end_odometer')->default(0);
            $table->double('real_start_odo')->default(0);
            $table->double('real_end_odo')->default(0);
            $table->text('start_location')->nullable();
            $table->text('end_location')->nullable();
            $table->bigInteger('client_id')->nullable();
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
        Schema::dropIfExists('keyoff_keyon_reports');
    }
};
