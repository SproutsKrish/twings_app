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
        Schema::create('share_links', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id')->nullable();
            $table->string('client_db_name')->nullable();
            $table->bigInteger('device_imei')->nullable();
            $table->dateTime('expiry_date')->nullable();
            $table->longText('link')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->ipAddress('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('share_links');
    }
};
