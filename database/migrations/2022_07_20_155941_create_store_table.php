<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoreTable extends Migration
{

    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->nullable();
            $table->string('store_image')->nullable();
            $table->integer('store_status')->default('1');
            $table->timestamps();
        });
    }

  
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
