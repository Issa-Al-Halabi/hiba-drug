<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBagsSettingTable extends Migration
{

    public function up()
    {
        Schema::create('bags_setting', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bag_id');
            $table->boolean('all')->default(0);
            $table->boolean('vip')->default(0);
            $table->boolean('non_vip')->default(0);
            $table->boolean('custom')->default(0);
            $table->text('group_ids');
            $table->timestamps();
            $table->foreign('bag_id')->references('id')->on('bag')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bags_setting');
    }
}
