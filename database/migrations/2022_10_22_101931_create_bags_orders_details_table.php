<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBagsOrdersDetailsTable extends Migration
{

    public function up()
    {
        Schema::create('bags_orders_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id');
            $table->bigInteger('bag_id');
            $table->bigInteger('seller_id');
            $table->text('bag_details');
            $table->integer('bag_qty');
            $table->double('bag_price');
            $table->double('bag_tax');
            $table->double('bag_discount');
            $table->string('delivery_status');
            $table->string('payment_status');
            $table->boolean('is_stock_decreased');
            $table->integer('refund_request');
            $table->timestamps();
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->string('order_type')->default("product");
        });

    }

    public function down()
    {
        Schema::dropIfExists('bags_orders_details');
    }
}
