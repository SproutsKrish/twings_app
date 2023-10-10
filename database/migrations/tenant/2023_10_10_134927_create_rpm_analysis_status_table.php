<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRpmAnalysisStatusTable extends Migration
{
    public function up()
    {
        Schema::create('rpm_analysis_status', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deviceimei')->default(0);
            $table->integer('ignition')->nullable();
            $table->double('speed')->default(0);
            $table->smallInteger('hour_meter_status')->nullable();
            $table->tinyInteger('rpm_status')->default(0);
            $table->double('battery_voltage')->nullable();
            $table->integer('frequency')->nullable();
            $table->integer('enginerpm')->nullable();
            $table->double('lattitute')->nullable();
            $table->double('longitute')->nullable();
            $table->datetime('device_updatetime');
            $table->smallInteger('packettype')->nullable();
            $table->datetime('create_datetime')->useCurrent();
            $table->timestamps();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rpm_analysis_status');
    }
}
