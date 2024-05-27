<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Customer extends Authenticatable
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'f_name',
        'l_name',
        'phone',
        'image',
        'email',
        'user_type',
        'city',
        'street_address',
        'country',
        'pharmacy_id',
    ];

    protected $hidden=[
        "email_verified_at","created_at","updated_at","zip","house_no",
        "apartment_no","cm_firebase_token","payment_card_last_four","payment_card_brand","payment_card_fawry_token","login_medium",
        "social_id","is_phone_verified","temporary_token","is_email_verified","pharmacy_id","area_id"

    ];

    use Notifiable, HasApiTokens;

}



