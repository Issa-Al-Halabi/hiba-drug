<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RewardItem extends Model
{
    use HasFactory;

    protected $fillable = [
        "product_id",
        "bag_id",
        "cost",
    ];

    
    public function getTypeId()
    {
        if ($this->product_id != null) {
            return $this->product_id;
        }
        return $this->bag_id;
    }

    public function getType()
    {
        if ($this->product_id != null) {
            return "product";
        }
        return "bag";
    }

    public function getPrice()
    {
        if ($this->product_id != null) {
            return $this->product->unit_price;
        }
        return $this->bag->total_price_offer;
    }

    public function scopeProducts($query)
    {
        return $query->where('bag_id', null);
    }

    public function scopeBags($query)
    {
        return $query->where('product_id', null);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function bag()
    {
        return $this->belongsTo(Bag::class, 'bag_id');
    }
}