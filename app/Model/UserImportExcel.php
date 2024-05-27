<?php

namespace App\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class UserImportExcel extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = "user_import_excel";

    protected $fillable = [
        'f_name', 'l_name', 'pharmacy_name','to','from', 'password','street_address', 'phone1', 'phone2','lat','lng', 'land_number','is_active','city_id','group_id','area_id','card_number','num_id'
    ];


    protected $hidden = [
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'integer',
        'is_phone_verified'=>'integer',
        'is_email_verified' => 'integer'
    ];

}
