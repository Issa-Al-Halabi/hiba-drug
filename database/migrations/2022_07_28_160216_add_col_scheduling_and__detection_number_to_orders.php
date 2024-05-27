<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColSchedulingAndDetectionNumberToOrders extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('scheduling');          //الجدولة
            $table->integer('Detection_number');   // رقم الكشف
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('scheduling');
            $table->dropColumn('Detection_number');
        });
    }
}
