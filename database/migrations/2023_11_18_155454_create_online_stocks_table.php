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
        Schema::create('online_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('barcode_no')->nullable();
            $table->bigInteger('sim_imei')->nullable();
            $table->bigInteger('sim_mob_no1')->nullable();
            $table->bigInteger('sim_mob_no2')->nullable();
            $table->bigInteger('device_imei')->nullable();
            $table->bigInteger('device_ccid')->nullable();
            $table->bigInteger('device_uid')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->bigInteger('dealer_id')->nullable();
            $table->bigInteger('subdealer_id')->nullable();
            $table->string('status')->default(1);
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->ipAddress('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('online_stocks');
    }
};
