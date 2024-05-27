<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomPharmacyToBagsSetting extends Migration
{

    public function up()
    {
        Schema::table('bags_setting', function (Blueprint $table) {
            $table->boolean('custom_pharmacy')->default(0);
            $table->text('pharmacy_ids');
        });
    }

    public function down()
    {
        Schema::table('bags_setting', function (Blueprint $table) {
            $table->dropColumn('custom_pharmacy');
             $table->dropColumn('pharmacy_ids');
        });
    }
}
