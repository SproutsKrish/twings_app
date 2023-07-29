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
            $table->string('name')->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('secondary_password')->nullable();

            // $table->bigInteger('client_id')->unsigned()->nullable();
            // $table->bigInteger('dealer_id')->unsigned()->nullable();
            // $table->bigInteger('subdealer_id')->unsigned()->nullable();
            // $table->bigInteger('role_id')->unsigned()->nullable();

            // $table->bigInteger('country_id')->unsigned()->nullable();
            // $table->string('country_name')->nullable();
            // $table->string('timezone_name')->nullable();
            // $table->string('timezone_offset')->nullable();
            // $table->integer('timezone_minutes')->nullable();

            $table->tinyInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // Use soft delete to handle deletion gracefully

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->ipAddress('ip_address')->nullable(); // Use ipAddress for IP address column

            // Foreign key constraints (if required)
            // $table->foreign('client_id')->references('id')->on('clients')->onDelete('set null');
            // $table->foreign('dealer_id')->references('id')->on('dealers')->onDelete('set null');
            // $table->foreign('subdealer_id')->references('id')->on('subdealers')->onDelete('set null');
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
            // $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');
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
