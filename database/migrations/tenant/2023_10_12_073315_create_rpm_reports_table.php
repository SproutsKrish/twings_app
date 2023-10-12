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
        Schema::create('rpm_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deviceimei');
            $table->integer('vehicle_id');
            $table->integer('client_id');
            $table->tinyInteger('flag')->nullable();
            $table->decimal('start_hour_meter', 10, 2)->nullable();
            $table->decimal('end_hour_meter', 10, 2)->nullable();
            $table->string('vehicle_name', 100)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('rpm_type', ['Idle', 'Load', 'OverLoad'])->nullable();
            $table->dateTime('created_datetime')->nullable();
            $table->timestamp('server_datetime')->useCurrent()->useCurrentOnUpdate();
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
        Schema::dropIfExists('rpm_reports');
    }
};
