<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddBrandIdToOrderDetails extends Migration
{

    public function up()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->bigInteger('brand_id')->default(1);
        });
    }

    public function down()
    {
        Schema::table('order_details', function (Blueprint $table) {
            $table->dropColumn('brand_id');
        });
    }
}
