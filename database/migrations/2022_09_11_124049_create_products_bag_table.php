<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsBagTable extends Migration
{

    public function up()
    {
        Schema::create('products_bag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('bag_id');
            $table->bigInteger('product_price');
            $table->bigInteger('product_count');
            $table->bigInteger('product_total_price');
            $table->bigInteger('is_gift')->default(0);
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('bag_id')->references('id')->on('bag')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('products_bag');
    }
}
