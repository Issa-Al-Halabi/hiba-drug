<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesPharmacyTable extends Migration
{
    public function up()
    {
        Schema::create('sales_pharmacy', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sales_id');
            $table->unsignedBigInteger('pharmacy_id');
            $table->timestamps();

            $table->foreign('sales_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('pharmacy_id')->references('id')->on('pharmacies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales_pharmacy');
    }
}
