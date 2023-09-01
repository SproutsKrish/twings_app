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
        Schema::table('sims', function (Blueprint $table) {
            $table->tinyInteger('sim_mob_no1')->nullable()->after("sim_imei_no");
            $table->tinyInteger('sim_mob_no2')->nullable()->after("sim_mob_no1");
            $table->dropColumn('sim_mob_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sims', function (Blueprint $table) {
        });
    }
};
