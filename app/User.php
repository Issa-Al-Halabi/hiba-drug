<?php

namespace App;

use App\Model\Order;
use App\Model\Review;
use App\Model\SalerReview;
use App\Model\ShippingAddress;
use App\Model\Wishlist;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $fillable = [
        'f_name', 'l_name', 'name', 'email', 'password', 'phone', 'image', 'login_medium','is_active','social_id','is_phone_verified','temporary_token','city','country','area_id'
    ];

    protected $hidden=[
        'password', 'remember_token',
        "email_verified_at","created_at","updated_at","zip","house_no",
        "apartment_no","cm_firebase_token","payment_card_last_four","payment_card_brand","payment_card_fawry_token","login_medium",
        "social_id","is_phone_verified","temporary_token","is_email_verified","pharmacy_id","area_id"
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'integer',
        'is_phone_verified'=>'integer',
        'is_email_verified' => 'integer'
    ];

    public function wish_list()
    {
        return $this->hasMany(Wishlist::class, 'customer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function shipping()
    {
        return $this->belongsTo(ShippingAddress::class, 'shipping_address');
    }

    public function pharmacy(){
        return $this->hasOne(Pharmacy::class,'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class,'user_id');
    }

    public function pharmacies()
    {
        return $this->hasMany(Pharmacy::class,'id');
    }

    public function work_plans()
    {
        return $this->hasMany(WorkPlan::class,'id');
    }

    //Sales Man Reviews
    public function saler_reviews()
    {
        return $this->hasMany(SalerReview::class,'saler_id','id');
    }

    public function saler_rating()
    {
        return $this->hasMany(SalerReview::class,'saler_id','id')
            ->select(DB::raw('avg(saler_rating) average, saler_id'))
            ->groupBy('saler_id');
    }
   //End Sales Man Reviews




}
