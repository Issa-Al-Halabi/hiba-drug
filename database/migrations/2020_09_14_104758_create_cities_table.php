<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{

    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->boolean('city_status')->default(1);
            $table->string('city_name');
            $table->timestamps();
        });
    }


    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
