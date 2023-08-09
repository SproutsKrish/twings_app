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
        Schema::create('customer_configurations', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->string('db_name',15)->nullable();
            $table->string('user_name',32)->nullable();
            $table->string('password',32)->nullable();
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
        Schema::dropIfExists('customer_configurations');
    }
};
