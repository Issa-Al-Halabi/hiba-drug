<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marketing extends Model
{
    use HasFactory;

    protected $table = "marketing";

    protected $fillable = ["id" ,"item_id", "item_type"];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

}
