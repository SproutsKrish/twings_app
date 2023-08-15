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
        Schema::create('alert_reports', function (Blueprint $table) {
            $table->id()->comment('Primary key');
            $table->bigInteger('route_id')->nullable()->comment('Route ID');
            $table->bigInteger('vehicle_id')->nullable()->comment('Vehicle ID');
            $table->string('vehicle_name', 30)->nullable()->comment('Vehicle name');
            $table->tinyInteger('type_id')->nullable()->comment('Type ID');
            $table->double('latitude')->nullable()->comment('Latitude');
            $table->double('longitude')->nullable()->comment('Longitude');
            $table->double('speed')->nullable()->comment('Speed');
            $table->bigInteger('phone_no')->nullable()->comment('Phone number');
            $table->string('deviation')->nullable()->comment('Deviation');
            $table->tinyInteger('status')->nullable()->comment('Status');
            $table->string('all_status', 11)->nullable()->comment('All status');
            $table->string('show_status', 11)->default('1')->comment('Show status');
            $table->string('sms_status', 11)->default('1')->comment('SMS status');
            $table->tinyInteger('play_status')->default('1')->comment('Play status');
            $table->tinyInteger('email_status')->default(0)->comment('Email status');
            $table->tinyInteger('send_sms_status')->default(0)->comment('Send SMS status');
            $table->string('geo_location_name', 45)->nullable()->comment('Geo location name');
            $table->longText('alert_location')->nullable()->comment('Alert location');
            $table->bigInteger('client_id')->nullable()->comment('Client ID');
            $table->bigInteger('dealer_id')->nullable()->comment('Dealer ID');
            $table->bigInteger('subdealer_id')->nullable()->comment('Subdealer ID');
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
        Schema::dropIfExists('alert_reports');
    }
};
