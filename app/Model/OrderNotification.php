<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use App\User;


class OrderNotification extends Model
{
    protected $table = 'order_notifications';
    protected $casts = [
        'user_id ' => 'integer',
    ];
    protected $fillable = [
        'user_id',
        'data',
        'created_at',
    ];
    protected $hidden=[
        'updated_at'
    ];
  	public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
