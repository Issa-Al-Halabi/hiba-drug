<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColNumIdToProducts extends Migration
{

    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('num_id');
        });
    }

   
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
