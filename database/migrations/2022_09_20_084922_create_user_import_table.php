<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserImportTable extends Migration
{
    public function up()
    {
        Schema::create('user_import_excel', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('num_id');
            $table->string('f_name');
            $table->string('l_name');
            $table->bigInteger('phone1');
            $table->bigInteger('phone2');
            $table->text('password');
            $table->string('street_address');
            $table->bigInteger('city_id');
            $table->bigInteger('group_id');
            $table->bigInteger('area_id');
            $table->boolean('is_active');
            $table->text('lat');
            $table->text('lng');
            $table->string('pharmacy_name');
            $table->time('to');
            $table->time('from');
            $table->bigInteger('land_number');
            $table->text('card_number');
            $table->timestamps();
        });

        // Schema::create('group_area', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->unsignedBigInteger('city_id');
        //     $table->string('group_name');
        //     $table->boolean('group_status')->default(1);
        //     $table->bigInteger('group_num')->default(0);
        //     $table->timestamps();
        //     $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        // });

        // Schema::create('areas', function (Blueprint $table) {
        //     $table->id();
        //     $table->unsignedBigInteger('group_id');
        //     $table->string('area_name');
        //     $table->boolean('area_status')->default(1);
        //     $table->bigInteger('area_num')->default(0);
        //     $table->timestamps();
        //     $table->foreign('group_id')->references('id')->on('group_area')->onDelete('cascade');

        // });

        // Schema::table('pharmacies', function (Blueprint $table) {
        //     $table->double('card_number');
        // });
    }

    public function down()
    {
        Schema::dropIfExists('user_import');
    }
}
