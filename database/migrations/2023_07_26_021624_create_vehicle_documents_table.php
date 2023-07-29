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
        Schema::create('vehicle_documents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_id');

            $table->string('policy_no');
            $table->string('insurance_company_name');
            $table->string('insurance_type');
            $table->date('insurance_start_date');
            $table->date('insurance_expiry_date');
            $table->string('insurance_front_image');
            $table->string('insurance_back_image');

            $table->date('fitness_certificate_expiry_date');
            $table->string('fitness_front_image');
            $table->string('fitness_back_image');

            $table->date('tax_expiry_date');
            $table->string('tax_front_image');
            $table->string('tax_back_image');

            $table->date('permit_expiry_date');
            $table->string('permit_front_image');
            $table->string('permit_back_image');

            $table->date('rc_expiry_date');
            $table->string('rc_front_image');
            $table->string('rc_back_image');

            $table->tinyInteger('status')->nullable()->default(1);

            $table->timestamps();
            $table->softDeletes(); // Use soft delete to handle deletion gracefully

            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('deleted_by')->unsigned()->nullable();
            $table->ipAddress('ip_address')->nullable(); // Use ipAddress for IP address column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vehicle_documents');
    }
};
