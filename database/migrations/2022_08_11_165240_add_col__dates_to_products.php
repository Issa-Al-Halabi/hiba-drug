<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColDatesToProducts extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('production_date');
            $table->date('expiry_date');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
