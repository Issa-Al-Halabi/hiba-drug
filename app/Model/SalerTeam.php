<?php

namespace App\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class SalerTeam extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = "salers_teams";

    protected $fillable = [
        'team', 'saler_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'saler_id'=>'integer',
    ];

}
