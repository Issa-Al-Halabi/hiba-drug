<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVipToPharmacy extends Migration
{

    public function up()
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            //
            $table->tinyInteger('vip');
        });
    }


    public function down()
    {
        Schema::table('pharmacies', function (Blueprint $table) {
            //
        });
    }
}
