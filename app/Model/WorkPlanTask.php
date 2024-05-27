<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WorkPlanTask extends Model
{
    protected $table = 'work_plan_tasks';

    protected $casts = [
        'task_plan_id '    => 'integer',
    ];

    protected $fillable = [
        'task_plan_id',
        'task_date',
        'pharmacies_ids'
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

}
