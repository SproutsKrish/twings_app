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
        Schema::create('vehicle_renewals', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('device_imei')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->date('old_vehicle_expire_date')->nullable();
            $table->date('new_vehicle_expire_date')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_renewals');
    }
};
