<?php

namespace App\Model;
use App\Pharmacy;
use App\User;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $casts = [
        'order_amount' => 'float',
        'discount_amount' => 'float',
        'customer_id' => 'integer',
        'shipping_address' => 'integer',
        'shipping_cost' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'billing_address'=> 'integer',
        'extra_discount'=>'float',
        'delivery_man_id'=>'integer',
        'shipping_method_id'=>'integer',
        'seller_id'=>'integer',
        'Detection_number'=>'integer',
        'cost_center'=>'integer',
        'order_receive_price'=>'integer',
        'order_note_price'=>'string',
    ];

    protected $hidden = [
        'third_party_delivery_tracking_id',
        'delivery_service_name',
        'delivery_type',
        'extra_discount_type',
        'billing_address_data' ,
        'billing_address',
        'delivery_man_id',
        'shipping_address_data',
        'coupon_code',
        'discount_type',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class)->orderBy('seller_id', 'ASC');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function sellerName()
    {
        return $this->hasOne(OrderDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address');
    }
    public function billingAddress()
    {
        return $this->belongsTo(ShippingAddress::class, 'billing_address');
    }

    public function delivery_man()
    {
        return $this->belongsTo(DeliveryMan::class,'delivery_man_id');
    }
  public function pharmacy()
    {
        return $this->belongsTo(Pharmacy::class, 'orderBy_id');
    }
}
