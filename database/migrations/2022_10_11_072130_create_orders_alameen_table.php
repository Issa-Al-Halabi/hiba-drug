<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersAlameenTable extends Migration
{

    public function up()
    {
        Schema::create('orders_alameen', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('pharmacy_id');
            $table->string('pharmacy_name');
            $table->text('product_details');    //object : (product_id , qty , price , q_gift)
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders_alameen');
    }
}
