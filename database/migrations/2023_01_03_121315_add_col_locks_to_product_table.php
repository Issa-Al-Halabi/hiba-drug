<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColLocksToProductTable extends Migration
{

    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string("locks")->default(0);
            $table->string("qty_locks")->default(0);
        });
    }

    public function down()
    {
        Schema::table('product', function (Blueprint $table) {
            $table->dropColumn('locks');
            $table->dropColumn('qty_locks');
        });
    }
}
