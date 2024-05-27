<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePharmaciesPlanDetailsTable extends Migration
{
    public function up()
    {
        Schema::create('pharmacies_plan_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('work_plan_id');
            $table->bigInteger('Wpharmacy_id');
            $table->boolean('visited')->default(0);
            $table->string('Wnote')->default(" ");
            $table->text('Wlat')->nullable();
            $table->text('Wlng')->nullable();
            $table->dateTime('visit_time')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pharmacies_plan_details');
    }
}
