<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            Schema::table('live_data', function (Blueprint $table) {
                $table->tinyInteger('new_ac_status')->nullable()->default(null)->after('ignition');
            });

            // Copy data from old column to new column
            DB::table('live_data')->update([
                'new_ac_status' => DB::raw('CAST(ac_status AS SIGNED)'),
            ]);

            // Drop the old column
            Schema::table('live_data', function (Blueprint $table) {
                $table->dropColumn('ac_status');
            });

            // Rename the new column to match the old name
            Schema::table('live_data', function (Blueprint $table) {
                $table->renameColumn('new_ac_status', 'ac_status');
            });


            //////

            Schema::table('live_data', function (Blueprint $table) {
                $table->tinyInteger('new_door_status')->nullable()->default(null)->after('ignition_report_datetime');
            });

            // Copy data from old column to new column
            DB::table('live_data')->update([
                'new_door_status' => DB::raw('CAST(door_status AS SIGNED)'),
            ]);

            // Drop the old column
            Schema::table('live_data', function (Blueprint $table) {
                $table->dropColumn('door_status');
            });

            // Rename the new column to match the old name
            Schema::table('live_data', function (Blueprint $table) {
                $table->renameColumn('new_door_status', 'door_status');
            });

            //////

            Schema::table('live_data', function (Blueprint $table) {
                $table->tinyInteger('new_temperature')->nullable()->default(null)->after('device_updatedtime');
            });

            // Copy data from old column to new column
            DB::table('live_data')->update([
                'new_temperature' => DB::raw('CAST(temperature AS SIGNED)'),
            ]);

            // Drop the old column
            Schema::table('live_data', function (Blueprint $table) {
                $table->dropColumn('temperature');
            });

            // Rename the new column to match the old name
            Schema::table('live_data', function (Blueprint $table) {
                $table->renameColumn('new_temperature', 'temperature');
            });
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
