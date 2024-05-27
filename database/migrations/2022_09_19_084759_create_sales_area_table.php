<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesAreaTable extends Migration
{

    public function up()
    {
        Schema::create('sales_area', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_id');
            $table->unsignedBigInteger('area_id');
            $table->foreign('sales_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('sales_area');
    }
}
