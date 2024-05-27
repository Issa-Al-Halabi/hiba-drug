<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BagsOrdersDetails extends Model
{
    protected $table = 'bags_orders_details';
    protected $casts = [
        'order_id '    => 'integer',
        'bag_id '     => 'integer',
        'seller_id' => 'integer',
        'bag_qty' => 'integer',
        'bag_price' => 'integer',
        'bag_tax' => 'integer',
        'bag_discount' => 'integer',
    ];
    protected $fillable = [
        'order_id',
        'bag_id',
        'seller_id',
        'bag_qty',
        'bag_price',
        'bag_tax',
        'bag_discount',
        'payment_status',
        'refund_request',
        'is_stock_decreased	',
        'delivery_status',
        'bag_details'
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

  
}
