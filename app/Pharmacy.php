<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Pharmacy extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'lat', 'lan', 'city', 'region','land_number', 'from', 'to', 'statusToday', 'Address','user_type_id'
    ];
    public function customer(){
        return $this->belongsTo(User::class,'user_id');
    }

}
