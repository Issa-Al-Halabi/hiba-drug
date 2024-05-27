<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PlanArchive extends Model
{
    protected $table = 'work_plan_archive';

    protected $fillable = [
        'begin_date',
        'end_date',
        'team_name',
        'saler_name',
        'pharmancies_visit_num',
        'orders_num',
        'pharmacies_num'
    ];

}
