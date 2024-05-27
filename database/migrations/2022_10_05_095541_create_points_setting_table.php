<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointsSettingTable extends Migration
{

    public function up()
    {
        Schema::create('products_points', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('type_id');
            $table->string('name');
            $table->bigInteger('quantity');
            $table->bigInteger('points');
            $table->timestamps();
        });

        Schema::create('orders_points', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('price');
            $table->bigInteger('points');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('points_setting');
    }
}
