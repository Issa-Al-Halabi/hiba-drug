<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColStoreIdToProductTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('store_id');
        });
    }
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('store_id');
        });
    }
}
