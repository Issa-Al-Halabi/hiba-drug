<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalersWorkPlansTable extends Migration
{

    public function up()
    {
        Schema::create('salers_work_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('begin_plan');
            $table->date('end_plan');
            $table->text('note');
            $table->unsignedBigInteger('saler_id');
            $table->string('saler_name');
            $table->text('pharmacies');
            $table->boolean('status_plan');
            $table->timestamps();
            $table->foreign('saler_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    public function down()
    {
        Schema::dropIfExists('salers_work_plans');
    }
}
