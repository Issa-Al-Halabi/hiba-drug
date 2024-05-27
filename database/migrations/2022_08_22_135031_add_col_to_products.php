<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColToProducts extends Migration
{

    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('scientific_formula');
            $table->integer('q_normal_offer')->default(0);
            $table->integer('q_featured_offer')->default(0);
            $table->integer('normal_offer')->default(0);
            $table->integer('featured_offer')->default(0);
            $table->integer('demand_limit')->default(0);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
