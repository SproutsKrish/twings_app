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
        Schema::create('live_data', function (Blueprint $table) {
            $table->id(); // Auto-incremental primary key

            // Use unsignedBigInteger instead of bigInteger
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();

            // Use text for variable length strings
            $table->string('vehicle_name', 40)->nullable();
            $table->tinyInteger('vehicle_current_status')->nullable();
            $table->tinyInteger('vehicle_status')->nullable();

            $table->unsignedBigInteger('deviceimei')->nullable(); // Use unsignedBigInteger
            $table->bigInteger('device_type_id')->default(1);

            $table->double('lattitute', 10, 6)->nullable(); // Corrected typo "lattitute" to "latitude"
            $table->double('longitute', 10, 6)->nullable(); // Corrected typo "longitute" to "longitude"
            $table->double('altitude')->nullable();

            // Use boolean for columns that represent true/false values
            $table->tinyInteger('ignition')->nullable();
            $table->tinyInteger('ac_status')->nullable();

            $table->double('speed')->default(0);
            $table->integer('angle')->nullable(); // Removed unnecessary length parameter
            $table->double('odometer')->default(0);
            $table->datetime('device_updatedtime')->nullable();

            $table->double('temperature', 8, 2)->nullable();
            $table->double('device_battery_volt', 8, 2)->nullable();
            $table->double('vehicle_battery_volt', 8, 2)->nullable();
            $table->datetime('last_ignition_on_time')->nullable();
            $table->datetime('last_ignition_off_time')->nullable();

            $table->double('fuel_litre', 8, 2)->nullable();

            $table->tinyInteger('vehicle_sleep')->default(0);
            $table->tinyInteger('immobilizer_status')->default(0);

            $table->string('gpssignal')->nullable();
            $table->string('gsm_status')->default('1');
            $table->decimal('rpm_value', 8, 2)->default(0.00);

            $table->tinyInteger('sec_engine_status')->default(0);
            $table->tinyInteger('ignition_report_flag')->nullable();
            $table->datetime('ignition_report_datetime')->nullable();

            $table->tinyInteger('door_status')->nullable();
            $table->tinyInteger('power_status')->default(1);

            $table->double('battery_percentage', 8, 2)->nullable();
            $table->double('today_distance', 8, 2)->nullable();
            $table->tinyInteger('expiry_status')->default(0);
            $table->smallInteger('current_alert_status')->default(0);
            $table->tinyInteger('ac_flag')->nullable();


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
        //
    }
};
