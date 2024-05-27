<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColDeliveryDateToOrders extends Migration
{

    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('delivery_date');
        });
    }


    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_date');
        });
    }
}
