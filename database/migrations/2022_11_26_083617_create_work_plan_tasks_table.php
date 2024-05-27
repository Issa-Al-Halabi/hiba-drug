<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkPlanTasksTable extends Migration
{

    public function up()
    {
        Schema::create('work_plan_tasks', function (Blueprint $table) {
            $table->id();
            $table->date("task_date");
            $table->unsignedInteger("task_plan_id");
            $table->bigInteger("pharmacy_id")->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_plan_tasks');
    }
}
