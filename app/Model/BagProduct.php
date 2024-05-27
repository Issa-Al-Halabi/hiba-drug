<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BagProduct extends Model
{
    protected $table = 'products_bag';
    protected $casts = [
        'product_id '=> 'integer',
        'bag_id '=> 'integer',
        'product_price'=> 'integer',
        'product_count'=> 'integer',
        'product_total_price'=> 'integer',
        'is_gift'=>'integer',
        'total_price_offer '    => 'integer',
        'bag_status '     => 'integer',
    ];
    protected $fillable = [
        'bag_name',
        'bag_description',
        'total_price_offer',
        'bag_image',
        'bag_status',
        'end_date',
    ];
    protected $hidden=[
        'created_at',
        'updated_at'
    ];
}
