<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class PlanDetailsArchive extends Model
{
    protected $table = 'work_plan_details_archive';

    protected $fillable = [
        'work_plan_archive_id ',
        'pharmacy_id',
        'pharmacy_name',
        'note',
        'site_match',
        'orders_num',
    ];
}
