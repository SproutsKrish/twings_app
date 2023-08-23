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
            $table->bigInteger('client_id');
            $table->string('link');
            $table->string('link_type');
            $table->timestamp('expiry_date')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Use soft delete to handle deletion gracefully
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
