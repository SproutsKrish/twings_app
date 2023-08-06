<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('keyoff_keyon_reports', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('flag')->nullable();
            $table->double('s_lat')->default(0);
            $table->double('s_lng')->default(0);
            $table->dateTime('start_day')->nullable();
            $table->dateTime('end_day')->nullable();
            $table->bigInteger('device_no')->nullable();
            $table->bigInteger('vehicle_id')->nullable();
            $table->double('total_km')->default(0);
            $table->double('e_lat')->default(0);
            $table->double('e_lng')->default(0);
            $table->smallInteger('type_id')->nullable();
            $table->string('fuel_usage', 45)->nullable();
            $table->string('fuel_filled', 45)->nullable();
            $table->string('initial_ltr', 45)->nullable();
            $table->string('end_ltr', 45)->nullable();
            $table->string('car_battery', 45)->nullable();
            $table->string('device_battery', 45)->nullable();
            $table->double('start_odometer')->default(0);
            $table->double('end_odometer')->default(0);
            $table->double('real_start_odo')->default(0);
            $table->double('real_end_odo')->default(0);
            $table->Text('start_location')->nullable();
            $table->Text('end_location')->nullable();
            $table->integer('client_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keyoff_keyon_reports');
    }
};
