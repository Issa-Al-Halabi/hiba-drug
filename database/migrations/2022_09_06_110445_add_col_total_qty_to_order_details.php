<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColTotalQtyToOrderDetails extends Migration
{

    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->bigInteger('total_qty')->default(0);
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            //
        });
    }
}
