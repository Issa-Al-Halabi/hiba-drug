<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PlanDetails extends Model
{
    protected $table = 'pharmacies_plan_details';
    protected $casts = [
        'work_plan_id '    => 'integer',
        'pharmacy_id '     => 'integer',
        'visited' => 'integer',
    ];
    protected $fillable = [
        'work_plan_id',
        'Wpharmacy_id',
        'visited',
        'Wnote',
        'Wlat',
        'Wlng',
        'visit_time',
        'created_at',
        'updated_at'
    ];

    public function workPlan()
    {
        return $this->belongsTo(PlanDetails::class);
    }
  
}
