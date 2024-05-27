<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPurePriceToCarts extends Migration
{

    public function up()
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->boolean('pure_price')->default(0);
        });

        Schema::table('order_details', function (Blueprint $table) {
            $table->boolean('pure_price')->default(0);
        });
    }

    public function down()
    {

        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('pure_price');
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropColumn('pure_price');
        });

    }
}
