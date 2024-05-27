<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColStatusExportToOrders extends Migration
{

    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('status_export')->default(0);
        });
    }


    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
