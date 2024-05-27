<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBonusTable extends Migration
{

    public function up()
    {
        Schema::create('bonuses', function (Blueprint $table) {
            $table->id();
            $table->text('master_product_id');
            $table->text('master_product_quatity');
            $table->text('salve_product_id');
            $table->text('salve_product_quatity');
            $table->integer('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bonus');
    }
}
