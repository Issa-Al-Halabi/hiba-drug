<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class BagsSetting extends Model
{
    protected $table = 'bags_setting';
    protected $casts = [
        'bag_id '    => 'integer',
        'all '     => 'integer',
        'vip' => 'integer',
        'non_vip' => 'integer',
        'custom' => 'integer',
    ];
    protected $fillable = [
        'bag_id',
        'all',
        'vip',
        'non_vip',
        'custom',
        'group_ids',
    ];

    protected $hidden=[
        'created_at',
        'updated_at'
    ];

    public function bag(){
        return $this->belongsTo(Bag::class,'bag_id');
    }

}
