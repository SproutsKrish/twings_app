<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('idle_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('flag')->default(0);
            $table->double('s_lat')->default(0);
            $table->double('s_lng')->default(0);
            $table->dateTime('start_day')->nullable();
            $table->dateTime('end_day')->nullable();
            $table->bigInteger('device_no')->nullable();
            $table->integer('vehicle_id')->default(0);
            $table->double('total_km')->default(0);
            $table->double('e_lat')->default(0);
            $table->double('e_lng')->default(0);
            $table->integer('type_id')->default(0);
            $table->longText('start_location')->nullable();
            $table->longText('end_location')->nullable();
            $table->integer('client_id')->default(0);
            $table->integer('updated_status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idle_reports');
    }
};
