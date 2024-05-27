<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitors extends Model
{
    use HasFactory;

    protected $table = "visitors";

    protected $fillable = ["serial_number"];

}
