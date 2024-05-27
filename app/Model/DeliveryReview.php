<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\CPU\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class DeliveryReview extends Model
{

    use HasFactory;

    protected $table = 'delivery_reviews';

    protected $casts = [

        'delivery_id'    => 'integer',
        'delivery_rating' => 'integer',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];

    protected $fillable = [
        'delivery_rating',
        'delivery_comment',
        'delivery_id',
        'emp_name'
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }

    public function getTitleAttribute($title)
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/seller')) {
            return $title;
        }

        return $this->translations[0]->value??$title;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')){
                    return $query->where('locale', App::getLocale());
                }else{
                    return $query->where('locale', Helpers::default_lang());
                }
            }]);
        });
    }

    public function delivery()
    {
        return $this->belongsTo(DeliveryMan::class, 'delivery_id');
    }


}
