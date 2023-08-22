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
        Schema::create('health_status', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('running_no');
            $table->tinyInteger('gpsv')->nullable();
            $table->tinyInteger('panic')->nullable();
            $table->double('bcharges', 8, 2)->default(0.00);
            $table->double('bchargel', 8, 2)->default(0.00);
            $table->double('internal_battery_voltage', 8, 2)->nullable();
            $table->dateTime('modified_date')->comment('Device Time');
            $table->double('mainbl', 8, 2)->nullable();
            $table->string('keyword', 20)->nullable();
            $table->string('pending', 20)->nullable();
            $table->tinyInteger('packet_type')->default(0)->comment('Packet Status (History/Live)');
            $table->string('vms', 20)->nullable();
            $table->text('packet')->nullable();
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
        Schema::dropIfExists('health_status');
    }
};
