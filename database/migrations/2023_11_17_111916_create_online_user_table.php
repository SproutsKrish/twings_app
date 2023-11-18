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
        Schema::create('online_users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->bigInteger('mobile_no')->nullable();
            $table->string('password');
            $table->bigInteger('role_id')->default(6);
            $table->bigInteger('country_id')->nullable();
            $table->longText('address')->nullable();
            $table->bigInteger('app_id')->nullable();
            $table->string('app_name')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->bigInteger('dealer_id')->nullable();
            $table->bigInteger('subdealer_id')->nullable();
            $table->string('status')->default("Pending");
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
        Schema::dropIfExists('online_users');
    }
};
