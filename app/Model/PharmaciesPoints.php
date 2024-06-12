<?php

namespace App\Model;

use App\Pharmacy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmaciesPoints extends Model
{
    protected $table="pharmacies_points";
    use HasFactory;
  
    protected $fillable = [
        "pharmacy_id",
        "points",
        "point_order_id",
    ];
  
    public function pharmacy(){
        return $this->belongsTo(Pharmacy::class,'pharmacy_id');
    }

}
