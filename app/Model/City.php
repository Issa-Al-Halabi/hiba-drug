<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $table='cities';
    protected $fillable=["id",'city_status', 'city_name'];
    protected $casts = [
        'id' => 'integer',
        'city_status' => 'integer',
    ];

    public function groups()
    {
        return $this->hasMany(Group::class);
    }
}
