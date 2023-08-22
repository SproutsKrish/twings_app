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
        Schema::create('fuel_analog', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('running_no');
            $table->double('latitude');
            $table->double('longtitude');
            $table->tinyInteger('speed')->default(0);
            $table->tinyInteger('flag')->default(0);
            $table->datetime('modified_date');
            $table->datetime('created_date');
            $table->integer('percent')->nullable();
            $table->double('raw_value')->default(0);
            $table->double('odometer')->nullable();
            $table->string('i1', 10)->default('0');
            $table->string('i2', 10)->default('0');
            $table->string('keyword', 20)->nullable();
            $table->double('litres', 8, 2)->nullable();
            $table->tinyInteger('packet_type')->default(0);
            $table->text('packet')->nullable();
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
        Schema::dropIfExists('fuel_analog');
    }
};
