<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PhoneOrEmailVerification extends Model
{
    protected $table = 'phone_or_email_verifications';
    protected $fillable = [
        'phone_or_email',
        'token',
    ];
    protected $hidden =[
        'updated_at',
        'created_at',
    ];

}
