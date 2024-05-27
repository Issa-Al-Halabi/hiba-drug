<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{

    protected $casts = [
        'product_id'  => 'integer',
        'customer_id' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')
        ->select(['id','slug','name','brand_id','unit','refundable','images','thumbnail',
        'featured','unit_price','purchase_price','current_stock','details','status',
        'featured_status','expiry_date','scientific_formula','q_normal_offer','q_featured_offer',
         'normal_offer','featured_offer','demand_limit','min_qty','locks','qty_locks'])->where('status','=',1);;
    }

    public function product_full_info()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
