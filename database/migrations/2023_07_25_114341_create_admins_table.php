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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();

            $table->string('admin_company')->nullable();
            $table->string('admin_name')->nullable();
            $table->string('admin_email')->nullable();
            $table->bigInteger('admin_mobile')->nullable();
            $table->longText('admin_address')->nullable();

            $table->string('admin_logo')->nullable();
            $table->integer('admin_limit')->nullable();
            $table->string('admin_city')->nullable();
            $table->string('admin_state')->nullable();
            $table->integer('admin_pincode')->nullable();

            $table->bigInteger('user_id')->nullable();

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
        Schema::dropIfExists('admins');
    }
};
