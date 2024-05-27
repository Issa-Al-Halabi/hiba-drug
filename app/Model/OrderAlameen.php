<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class OrderAlameen extends Model
{
    protected $table = 'orders_alameen';
    protected $casts = [
        'order_id '    => 'integer',
        'pharmacy_id '     => 'integer',
        'cost_center' => 'integer',
        'Detection_number' => 'integer',
    ];
    protected $fillable = [
        'order_id',
        'pharmacy_id',
        'pharmacy_name',
        'product_details',
        'cost_center',
        'Detection_number',
        'created_at',
    ];

    protected $hidden=[
        'updated_at'
    ];
}
