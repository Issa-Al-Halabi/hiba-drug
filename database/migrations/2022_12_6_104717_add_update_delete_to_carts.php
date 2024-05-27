<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUpdateDeleteToCarts extends Migration
{

    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->boolean('update_delete')->default(1);
        });
    }

    public function down()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('update_delete');
        });
    }
}
