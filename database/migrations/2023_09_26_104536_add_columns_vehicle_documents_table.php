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
        Schema::table('vehicle_documents', function (Blueprint $table) {
            $table->bigInteger('client_id')->nullable()->after('id');
            $table->bigInteger('device_imei')->nullable()->after('vehicle_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicle_documents', function (Blueprint $table) {
            //
        });
    }
};
