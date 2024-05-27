<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesGroupTable extends Migration
{

    public function up()
    {
        Schema::create('sales_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_id');
            $table->unsignedBigInteger('group_id');
            $table->foreign('sales_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('group_area')->onDelete('cascade');
            $table->timestamps();
        });
    }

    
    public function down()
    {
        Schema::dropIfExists('sales_group');
    }
}
