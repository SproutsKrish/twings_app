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
        Schema::create('immobilizer_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->bigInteger('vehicle_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('completed_status')->nullable();
            $table->text('address')->nullable();
            $table->integer('device_port')->nullable();
            $table->string('device_name')->nullable();
            $table->bigInteger('dealer_id');
            $table->bigInteger('subdealer_id');
            $table->bigInteger('created_by');
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
        Schema::dropIfExists('immobilizer_data');
    }
};
