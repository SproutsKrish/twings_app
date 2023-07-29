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
        Schema::create('distributors', function (Blueprint $table) {
            $table->id();

            $table->string('distributor_company')->nullable();
            $table->string('distributor_name')->nullable();
            $table->string('distributor_email')->nullable();
            $table->bigInteger('distributor_mobile')->nullable();
            $table->longText('distributor_address')->nullable();

            $table->string('distributor_logo')->nullable();
            $table->integer('distributor_limit')->nullable();
            $table->string('distributor_city')->nullable();
            $table->string('distributor_state')->nullable();
            $table->integer('distributor_pincode')->nullable();

            $table->bigInteger('country_id')->nullable();
            $table->string('country_name')->nullable();
            $table->string('timezone_name')->nullable();
            $table->string('timezone_offset')->nullable();
            $table->integer('timezone_minutes')->nullable();

            $table->bigInteger('admin_id')->nullable();
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
        Schema::dropIfExists('distributors');
    }
};
