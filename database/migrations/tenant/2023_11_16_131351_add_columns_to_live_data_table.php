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
            $table->tinyInteger('safe_parking')->default(0)->after('end_odometer');
            $table->tinyInteger('safe_parking_alert')->default(0)->after('ac_flag');
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
            //
        });
    }
};
