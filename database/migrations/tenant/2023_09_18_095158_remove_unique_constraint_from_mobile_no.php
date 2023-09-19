<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveUniqueConstraintFromMobileNo extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['mobile_no']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // You can add back the unique constraint in the down method if needed
            // $table->unique('mobile_no');
        });
    }
}