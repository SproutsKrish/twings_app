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
        Schema::create('dealers', function (Blueprint $table) {
            $table->id();

            $table->string('dealer_company')->nullable();
            $table->string('dealer_name')->nullable();
            $table->string('dealer_email')->nullable();
            $table->bigInteger('dealer_mobile')->nullable();
            $table->longText('dealer_address')->nullable();

            $table->string('dealer_logo')->nullable();
            $table->integer('dealer_limit')->nullable();
            $table->string('dealer_city')->nullable();
            $table->string('dealer_state')->nullable();
            $table->integer('dealer_pincode')->nullable();

            $table->string('dealer_domain')->nullable();

            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('admin_id')->nullable();
            $table->bigInteger('distributor_id')->nullable();
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
        Schema::dropIfExists('dealers');
    }
};
