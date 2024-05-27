<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsKeys extends Model
{
    use HasFactory;

    protected $table = "products_keys";

    protected $fillable = ["id" ,"key_id", "cus_id", "base_product_id","other_product_id"];

    protected $casts = [
        'id' => 'integer',
        'key_id' => 'integer',
        'cus_id' => 'integer',
        'base_product_id'=>'integer'
    ];


}
