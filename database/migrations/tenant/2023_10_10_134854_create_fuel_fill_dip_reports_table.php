<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('fuel_fill_dip_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('vehicle_id');
            $table->bigInteger('deviceimei');
            $table->integer('client_id');
            $table->decimal('start_fuel', 10, 2)->nullable();
            $table->decimal('end_fuel', 10, 2)->nullable();
            $table->decimal('fuel_difference', 10, 2)->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->string('location_name', 255)->nullable();
            $table->enum('report_type', ['Fill', 'Dip'])->nullable();
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->datetime('created_datetime')->nullable();
            $table->timestamp('server_time')->useCurrent()->onUpdateCurrent();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fuel_fill_dip_reports');
    }
};
