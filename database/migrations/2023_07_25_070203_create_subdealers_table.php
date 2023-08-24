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
        Schema::create('sub_dealers', function (Blueprint $table) {
            $table->id();
            $table->string('subdealer_company')->nullable();
            $table->string('subdealer_name')->nullable();
            $table->string('subdealer_email')->nullable();
            $table->bigInteger('subdealer_mobile')->nullable();
            $table->longText('subdealer_address')->nullable();

            $table->string('subdealer_logo')->nullable();
            $table->integer('subdealer_limit')->nullable();
            $table->string('subdealer_city')->nullable();
            $table->string('subdealer_state')->nullable();
            $table->integer('subdealer_pincode')->nullable();

            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
            $table->bigInteger('dealer_id')->nullable();
            $table->tinyInteger('status')->nullable()->default(1);

            $table->timestamps();
            $table->softDeletes(); // Add this line to enable soft delete
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->string('ip_address')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sub_dealers');
    }
};
