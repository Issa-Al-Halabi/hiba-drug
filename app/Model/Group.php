<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = "group_area";

    protected $fillable = ["id" ,"group_name", "group_status", "city_id","group_num"];

    protected $casts = [
        'id' => 'integer',
        'group_status' => 'integer',
        'city_id' => 'integer',
        'group_num' => 'integer',
    ];

    public function city() {
      return $this->belongsTo(City::class, 'city_id', 'id');
    }

    public function areas()
    {
        return $this->hasMany(Area::class);
    }
}
