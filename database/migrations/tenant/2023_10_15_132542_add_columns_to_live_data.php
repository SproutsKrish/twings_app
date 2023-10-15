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
        Schema::table('live_data', function (Blueprint $table) {
            $table->tinyInteger('odometer_flag')->nullable()->after('current_alert_status');
            $table->dateTime('odometer_updatedate')->nullable()->after('odometer_flag');
            $table->double('start_odometer')->nullable()->after('odometer_updatedate');
            $table->double('end_odometer')->nullable()->after('start_odometer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('live_data', function (Blueprint $table) {
            $table->dropColumn(['odometer_flag', 'odometer_updatedate', 'start_odometer', 'end_odometer']);
        });
    }
};
