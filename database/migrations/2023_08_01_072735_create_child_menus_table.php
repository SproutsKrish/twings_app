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
        Schema::create('child_menus', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parent_menu_id');
            $table->string('child_menu_name');
            $table->string('child_menu_icon');
            $table->string('child_menu_url');
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
        Schema::dropIfExists('child_menus');
    }
};
