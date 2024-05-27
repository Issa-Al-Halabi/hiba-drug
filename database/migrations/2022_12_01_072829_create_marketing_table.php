<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketingTable extends Migration
{

    public function up()
    {
        Schema::create('marketing', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("item_id");
            $table->string("item_type");
            // $table->text("item_priority");
            // $table->date("item_rate");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marketing');
    }
}
