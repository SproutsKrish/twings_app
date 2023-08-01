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
        Schema::create('parent_menus', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('module_id');
            $table->string('parent_menu_name');
            $table->string('parent_menu_icon');
            $table->string('parent_menu_url');
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
        Schema::dropIfExists('parent_menus');
    }
};
