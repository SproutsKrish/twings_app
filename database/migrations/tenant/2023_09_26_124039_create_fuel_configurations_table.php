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
        Schema::create('fuel_configurations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->string('db_name', 60)->nullable();
            $table->bigInteger('deviceimei')->nullable();
            $table->integer('device_type_id')->nullable();
            $table->integer('fuel_model_id')->nullable();
            $table->integer('fuel_type_id')->nullable();
            $table->integer('fuel_tank_type_id')->nullable();
            $table->string('fuel_formula', 100)->nullable();
            $table->string('fuel_a_value', 60)->nullable();
            $table->string('fuel_b_value', 60)->nullable();
            $table->string('fuel_c_value', 60)->nullable();
            $table->string('fuel_d_value', 60)->nullable();
            $table->string('fuel_tank_capacity', 19)->nullable();
            $table->tinyInteger('status')->nullable();
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
        Schema::dropIfExists('fuel_configurations');
    }
};
