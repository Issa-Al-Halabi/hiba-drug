<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBagTable extends Migration
{

    public function up()
    {
        Schema::create('bag', function (Blueprint $table) {
            $table->id();
            $table->string('bag_name');
            $table->string('bag_description');
            $table->bigInteger('total_price_offer');
            $table->string('bag_image');
            $table->boolean('bag_status')->default(0);
            $table->date('end_date');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bag');
    }
}
