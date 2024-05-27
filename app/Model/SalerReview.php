<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\CPU\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class SalerReview extends Model
{

    use HasFactory;

    protected $table = 'salers_reviews';

    protected $casts = [

        'saler_id'    => 'integer',
        'saler_rating' => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    protected $fillable = [
        'saler_rating',
        'saler_comment',
        'saler_id',
        'emp_name'
    ];


    public function saler()
    {
        return $this->belongsTo(User::class, 'saler_id');
    }

}
