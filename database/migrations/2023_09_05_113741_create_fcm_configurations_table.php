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
        Schema::create('fcm_configurations', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('mobile_type')->nullable();
            $table->string('mobile_model')->nullable();
            $table->string('application_name')->nullable();
            $table->string('server_key')->nullable();
            $table->longText('fcm_token')->nullable();
            $table->longText('access_token')->nullable();
            $table->bigInteger('token_id')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('role_id')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('fcm_configurations');
    }
};
