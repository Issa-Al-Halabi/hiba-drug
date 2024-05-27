<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $table = "areas";

    protected $fillable = ["id" ,"area_name", "area_status", "group_id","area_num"];

    protected $casts = [
        'id' => 'integer',
        'area_status' => 'integer',
        'group_id' => 'integer',
        'area_num' => 'integer',
    ];

    public function group() {
      return $this->belongsTo(Group::class, 'group_id', 'id');
    }
}
