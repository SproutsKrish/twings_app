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
        Schema::create('parking_reports', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('flag')->default(0);
            $table->double('start_latitude')->default(0);
            $table->double('start_longitude')->default(0);
            $table->datetime('start_datetime')->nullable();
            $table->datetime('end_datetime')->nullable();
            $table->bigInteger('device_imei')->nullable();
            $table->bigInteger('vehicle_id')->default(0);
            $table->double('total_km')->default(0);
            $table->double('end_latitude')->default(0);
            $table->double('end_longitude')->default(0);
            $table->integer('type_id')->default(0);
            $table->longText('start_location')->nullable();
            $table->longText('end_location')->nullable();
            $table->bigInteger('client_id')->default(0);
            $table->integer('updated_status')->default(0);
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
        Schema::dropIfExists('parking_reports');
    }
};
