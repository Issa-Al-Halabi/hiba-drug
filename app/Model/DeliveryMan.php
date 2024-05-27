<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DeliveryMan extends Model
{

    protected $hidden = ['password','auth_token'];


    protected $casts = [
        'is_active'=>'integer'
    ];



    //table delivery reviews
    public function reviews()
    {
        return $this->hasMany(DeliveryReview::class,'delivery_id','id');
    }


    public function rating()
    {
        return $this->hasMany(DeliveryReview::class,'delivery_id','id')
            ->select(DB::raw('avg(delivery_rating) average, delivery_id'))
            ->groupBy('delivery_id');
    }

    

}
