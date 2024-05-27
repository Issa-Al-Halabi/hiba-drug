<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkPlanArchiveTable extends Migration
{

    public function up()
    {
        Schema::create('work_plan_archive', function (Blueprint $table) {
            $table->id();
            $table->date("begin_date");
            $table->date("end_date");
            $table->string("team_name");
            $table->bigInteger("saler_id");
            $table->string("saler_name");
            $table->bigInteger("pharmancies_visit_num");
            $table->bigInteger("orders_num");
            $table->timestamps();
        });

        Schema::create('work_plan_details_archive', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("work_plan_archive_id");
            $table->bigInteger("pharmacy_id");
            $table->string("pharmacy_name");
            $table->text("note");
            $table->date("visit_date");
            $table->boolean("site_match");
            $table->bigInteger("orders_num");
            $table->timestamps();
            $table->foreign('work_plan_archive_id')->references('id')->on('work_plan_archive')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_plan_archive');
        Schema::dropIfExists('work_plan_details_archive');
    }
}
