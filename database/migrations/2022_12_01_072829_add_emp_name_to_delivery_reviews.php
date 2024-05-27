<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmpNameToDeliveryReviews extends Migration
{

    public function up()
    {
        Schema::table('delivery_reviews', function (Blueprint $table) {
            $table->string("emp_name");
        });
    }

    public function down()
    {
        Schema::table('delivery_reviews', function (Blueprint $table) {
            $table->dropColumn('emp_name');
        });
    }
}
