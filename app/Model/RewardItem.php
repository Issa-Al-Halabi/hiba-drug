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
