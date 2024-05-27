<?php

namespace App\Services;
use App\Model\BagProduct;
use App\Model\Brand;
use App\Model\Product;

class BagServices
{

    public static function getProductsBybag($bagId)
    {
        $bagProducts = BagProduct::where(['bag_id' => $bagId])->get();
        foreach ($bagProducts as $bagProduct) {
            $p = Product::whereid($bagProduct->product_id)->first();
            $b = Brand::whereid($p->brand_id)->first();
            $bagProduct['product_name'] = $p->name;
            $bagProduct['brand_name'] = $b->name;
            $bagProduct['thumbnail'] = $p->thumbnail;
            $bagProduct['expiry_date'] = $p->expiry_date;
        }
        return $bagProducts;
    }

}
