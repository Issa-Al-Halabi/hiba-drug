<?php

namespace App\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class WorkPlan extends Authenticatable
{
    use Notifiable, HasApiTokens;

    protected $table = "salers_work_plans";

    protected $fillable = [
        'begin_plan', 'end_plan', 'note','saler_id','pharmacies','saler_name'
    ];


    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'saler_id'=>'integer',
    ];

  
    public function details()
    {
        return $this->hasMany(PlanDetails::class);
    }
  
}
