<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalersReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('salers_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("saler_id");
            $table->text("saler_comment");
            $table->integer("saler_rating");
            $table->string("emp_name");
            $table->timestamps();
            $table->foreign('saler_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('salers_reviews');
    }

    
}
