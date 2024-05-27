<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('delivery_id');
            $table->string('delivery_comment');
            $table->integer('delivery_rating')->default(0);
            $table->timestamps();
            $table->foreign('delivery_id')->references('id')->on('delivery_men')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_reviews');
    }
}
