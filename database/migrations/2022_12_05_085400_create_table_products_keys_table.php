<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableProductsKeysTable extends Migration
{

    public function up()
    {
        Schema::create('products_keys', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("key_id");
            $table->bigInteger("cus_id");
            $table->bigInteger("base_product_id");   //unique
            $table->text("other_product_id");
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('table_products_keys');
    }
}
