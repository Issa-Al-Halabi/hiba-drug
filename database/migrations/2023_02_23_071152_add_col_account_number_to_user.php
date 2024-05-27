<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColAccountNumberToUser extends Migration
{

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger("account_number")->default(0);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('account_number');
        });
    }
}
