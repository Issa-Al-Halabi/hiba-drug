<?php

namespace App\CPU;

use App\Model\Brand;
use App\Model\Product;
use App\Model\ProductPoint;
use App\Model\Bonus;
use Illuminate\Support\Facades\Cache;
class BrandManager
{
    public static function get_brands()
    {
        $cipherUrl=BrandManager::cacheKey(url()->current());
        if (Cache::has($cipherUrl)) {
            $brands = Cache::get($cipherUrl);
        } else {
            $brands = Brand::withCount('brandProducts')->orderBy('name')->latest()->get();
            Cache::add($cipherUrl, $brands, 30);
        }
        return $brands;
    }

    public static function get_products($brand_id)
    {

        $cipherUrl=BrandManager::cacheKey(url()->current());
        if (Cache::has($cipherUrl)) {
            $products = Cache::get($cipherUrl);
        } else {
           $products = Product::active()->with(['rating'])->where(['brand_id' => $brand_id])->where('status', '=', 1)
            ->orderBy('name')
            ->orderBy('current_stock', 'desc')
            ->get()
            ->map(function ($item) {
                   return $item->makeHidden( [
                    'added_by', 'user_id', 'category_ids',
                    'flash_deal', 'video_provider', 'video_url', 'colors',
                    'variant_product', 'attributes', 'choice_options', 'variation',
                    'published', 'tax', 'tax_type', 'attachment',
                    'meta_title', 'meta_description', 'meta_image', 'request_status', 'denied_note',
                    'temp_shipping_cost', 'is_shipping_cost_updated', 'store_id', 'num_id',
                    'created_at', 'updated_at', 'min_qty', 'multiply_qty', 'shipping_cost',
                ]);
             });
            Cache::add($cipherUrl, $products, 10);
        }

        //$points = ProductPoint::where('type', 'product')->get();
        $pointNew = "0";
        foreach ($products as $p) {

            // foreach ($points as $point) {
            //     $idx = json_decode($point->type_id);
            //     foreach ($idx as $d) {
            //         if ($p['id'] == $d) {
            //             $pointNew = $point->points;
            //         } else {
            //             $pointNew = "0";
            //         }
            //     }
            // }
            $p['points'] = $pointNew;
        }
        return $products;
    }

    public static function cacheKey($request)
    {
        return md5(url()->current());
    }
}
