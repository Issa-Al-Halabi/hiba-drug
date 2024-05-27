<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalersTeamsTable extends Migration
{

    public function up()
    {
        Schema::create('salers_teams', function (Blueprint $table) {
            $table->id();
            $table->string('team')->default("A");
            $table->unsignedBigInteger('saler_id');
            $table->timestamps();
            $table->foreign('saler_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('salers_teams');
    }
}
