<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderNotificationsTable extends Migration
{

    public function up()
    {
        Schema::create('order_notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("user_id");
            $table->text("data");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_notifications');
    }
}
