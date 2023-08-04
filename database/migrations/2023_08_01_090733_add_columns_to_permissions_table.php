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
        Schema::table('permissions', function (Blueprint $table) {
            $table->integer('module_id')->nullable()->after('id');
            $table->integer('parent_menu_id')->nullable()->after('module_id');
            $table->integer('child_menu_id')->nullable()->after('parent_menu_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('module_id');
            $table->dropColumn('parent_menu_id');
            $table->dropColumn('child_menu_id');
        });
    }
};
