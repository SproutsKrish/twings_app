<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('live_data', function (Blueprint $table) {
            $table->renameColumn('assign_geofence_id', 'assign_georeport_id');
        });
    }

    public function down()
    {
        Schema::table('live_data', function (Blueprint $table) {
            $table->renameColumn('assign_georeport_id', 'assign_geofence_id');
        });
    }
};
