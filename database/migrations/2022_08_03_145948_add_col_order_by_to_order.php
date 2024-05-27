<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColOrderByToOrder extends Migration
{

    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('orderBy_id')->default(null);//اذا كان مندوب يتم تعبئة الحقل برقم الصيدلية التي يتم طلب لها
        });
    }


    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('orderBy_id');
        });
    }
}
