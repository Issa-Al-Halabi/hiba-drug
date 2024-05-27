<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColCostCenterToOrders extends Migration
{

    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->bigInteger("cost_center")->default(0);
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('cost_center');
        });
    }
}
