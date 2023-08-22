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
        Schema::create('alert_status', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('alert_type_id')->nullable()->comment('alert_type table alert_id');
            $table->bigInteger('deviceimei')->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->tinyInteger('ignition')->default(0);
            $table->tinyInteger('ac_status')->default(0);
            $table->float('speed')->default(0);
            $table->double('odometer')->nullable();
            $table->dateTime('device_datetime')->nullable()->comment('device datetime');
            $table->tinyInteger('packet_status')->default(0)->comment('0 - live 1- History');
            $table->longText('packet')->nullable()->comment('Raw value');
            $table->string('transmission_type', 10)->nullable();
            $table->string('gps_satellite', 30)->nullable();
            $table->tinyInteger('gsm_status')->nullable();
            $table->string('device_name', 30)->nullable();
            $table->dateTime('server_time')->nullable()->comment('jar file inserted datetime');
            $table->dateTime('created_time')->nullable()->comment('jar file inserted datetime');
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
        Schema::dropIfExists('alert_status');
    }
};
