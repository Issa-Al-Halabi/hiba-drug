<?php

namespace App\Model;

use App\CPU\Helpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
//use Spatie\Translatable\HasTranslations;

class Product extends Model
{
  // use HasTranslations;
  // public $translatable = ['name', 'details'];

    protected $appends = ['category_names'];
    protected $casts = [
        'user_id' => 'integer',
        'brand_id' => 'integer',
        'min_qty' => 'integer',
        'published' => 'integer',
        'tax' => 'float',
        'unit_price' => 'float',
        'status' => 'integer',
        'discount' => 'float',
        'current_stock' => 'integer',
        'free_shipping' => 'integer',
        'featured_status' => 'integer',
        'refundable' => 'integer',
        'featured' => 'integer',
        'flash_deal' => 'integer',
        'seller_id' => 'integer',
        'purchase_price' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'shipping_cost' => 'float',
        'pure_price_status' => 'integer',
        'multiply_qty' => 'integer',
        'temp_shipping_cost' => 'float',
        'is_shipping_cost_updated' => 'integer',
        'q_normal_offer' => 'integer',
        'normal_offer' => 'integer',
        'q_featured_offer' => 'integer',
        'featured_offer' => 'integer',
        'demand_limit' => 'integer',
        'reviews_count' => 'integer',
    ];

    protected $hidden = [
        "category_ids", "video_provider", "video_url", "colors", "variant_product", "attributes", "choice_options",
        "variation", "published", "tax", "tax_type", "discount", "discount_type", "free_shipping", "attachment", "created_at",
        "updated_at", "meta_title", "meta_description", "meta_image", "request_status", "denied_note", "shipping_cost",
        "multiply_qty", "temp_shipping_cost", "is_shipping_cost_updated", "store_id", "production_date", "num_id", "translations"
    ];

    public function getCategoryNamesAttribute()
    {
        $category_names = "_";

        $category_ids = json_decode($this->category_ids, true);
        $ids = array_column($category_ids, 'id');

        $categories = Category::whereIn('id', $ids)->get();

        foreach ($categories as $category) {
            $category_names = $category->name;
        }

        return $category_names;
    }


    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }


    public function scopeActive($query)
    {
        return $query->whereHas('seller', function ($query) {
            $query->where(['status' => 'approved']);
        })->where(['status' => 1])->orWhere(function ($query) {
            $query->where(['added_by' => 'admin', 'status' => 1]);
        });
    }

    public function stocks()
    {
        return $this->hasMany(ProductStock::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    //Done
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function scopeStatus($query)
    {
        return $query->where('featured_status', 1);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'seller_id');
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class, 'user_id');
    }

    public function rating()
    {
        return $this->hasMany(Review::class)
            ->select(DB::raw('avg(rating) average, product_id'))
            ->groupBy('product_id');
    }

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    public function order_delivered()
    {
        return $this->hasMany(OrderDetail::class, 'product_id')
            ->where('delivery_status', 'delivered');
    }


    public function order_delivered_offers()
    {
        return $this->hasMany(OrderDetail::class, 'product_id')
            ->where('delivery_status', 'delivered');
    }

    public function wish_list()
    {
        return $this->hasMany(Wishlist::class, 'product_id');
    }

    public function getNameAttribute($name)
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/seller')) {
            return $name;
         }
         return $this->translations[0]->value ?? $name;
     //   if (app()->getLocale() == "en" && $name == "") {
      //      return $this->getTranslation("name", "ar");
      //  }
      //  return $name;
    }

    public function getDetailsAttribute($detail)
    {
        if (strpos(url()->current(), '/admin') || strpos(url()->current(), '/seller')) {
            return $detail;
        }
        return $this->translations[1]->value ?? $detail;
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('translate', function (Builder $builder) {
            $builder->with(['translations' => function ($query) {
                if (strpos(url()->current(), '/api')) {
                    return $query->where('locale', App::getLocale());
                } else {
                    return $query->where('locale', Helpers::default_lang());
                }
            }, 'reviews'])->withCount('reviews');
        });
    }
}
