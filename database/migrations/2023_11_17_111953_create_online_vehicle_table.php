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
        Schema::create('online_vehicles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('online_user_id')->nullable();
            $table->bigInteger('barcode_no')->nullable()->index();
            $table->bigInteger('vehicle_type_id')->nullable();
            $table->string('vehicle_name')->nullable();
            $table->bigInteger('app_id')->nullable();
            $table->string('app_package_name')->nullable()->index();
            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->bigInteger('dealer_id')->nullable();
            $table->bigInteger('subdealer_id')->nullable();
            $table->string('status')->default("Pending")->index();
            $table->timestamps();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('online_vehicles');
    }
};
