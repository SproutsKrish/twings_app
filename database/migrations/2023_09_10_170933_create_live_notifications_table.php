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
        Schema::create('live_notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('device_imei')->nullable();
            $table->bigInteger('alert_type_id')->nullable();

            $table->double('lattitute', 10, 6)->nullable();
            $table->double('longitute', 10, 6)->nullable();
            $table->tinyInteger('ignition')->nullable();
            $table->double('speed')->default(0);
            $table->integer('angle')->nullable();
            $table->double('odometer')->default(0);

            $table->datetime('device_updatedtime')->nullable();
            $table->tinyInteger('ignition_flag')->nullable();
            $table->tinyInteger('notification_status')->default(0);

            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists('live_notifications');
    }
};
