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
        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->nullable();
            $table->string('service_type')->nullable();
            $table->string('purchase_product')->nullable();
            $table->float('purchase_amount')->nullable();
            $table->string('payment_mode')->nullable();
            $table->string('mode_details')->nullable();
            $table->date('purchase_date')->nullable();
            $table->string('description')->nullable();
            $table->date('reminder_date')->nullable();
            $table->string('reminder_km')->nullable();

            $table->tinyInteger('status')->nullable()->default(1);

            $table->timestamps();
            $table->softDeletes();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->ipAddress('ip_address')->nullable(); // Use ipAddress for IP address column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_services');
    }
};
