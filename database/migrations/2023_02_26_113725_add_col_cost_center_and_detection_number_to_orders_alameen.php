<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColCostCenterAndDetectionNumberToOrdersAlameen extends Migration
{

    public function up()
    {
        Schema::table('orders_alameen', function (Blueprint $table) {
            $table->bigInteger("cost_center")->default(0);
            $table->bigInteger("Detection_number")->default(0);
            $table->date("delivery_date")->default(now());
            $table->string("customer_type");
        });
    }

    public function down()
    {
        Schema::table('orders_alameen', function (Blueprint $table) {
            $table->dropColumn('cost_center');
            $table->dropColumn('Detection_number');
            $table->dropColumn("delivery_date");
            $table->dropColumn("customer_type");
        });
    }
}
