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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->bigInteger('mobile_no')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('secondary_password')->nullable();
            $table->bigInteger('role_id')->nullable();

            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->bigInteger('dealer_id')->nullable();
            $table->bigInteger('subdealer_id')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->bigInteger('vehicle_owner_id')->nullable();
            $table->bigInteger('staff_id')->nullable();

            $table->bigInteger('country_id')->nullable();
            $table->string('country_name')->nullable();
            $table->string('timezone_name')->nullable();
            $table->string('timezone_offset')->nullable();
            $table->integer('timezone_minutes')->nullable();

            $table->tinyInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
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
        Schema::dropIfExists('users');
    }
};
