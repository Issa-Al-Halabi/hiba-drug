<?php

namespace App\CPU;

use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\ProductPoint;
use App\Model\Bonus;
use App\Model\Review;
use App\Model\Marketing;
use App\Model\ShippingMethod;
use App\Model\Translation;
use Illuminate\Support\Facades\DB;

class ProductManager
{
    public static function get_product($id)
    {
        return Product::active()->with(['rating'])->where('id', $id)->first();
    }

    public static function get_latest_products($brand_id , $limit = 100, $offset = 1)
    {
        if (isset($brand_id)) {
            $paginator = Product::active()->where('brand_id', '=', $brand_id)->with(['rating'])->latest()->paginate(3000, ['*'], 'page', $offset);
            //ProductManager::add_points($paginator,$paginator->items());
           // ProductManager::add_locks($paginator,$paginator->items());
        } else {
            $paginator = Product::active()->with(['rating'])->latest()->paginate(3000, ['*'], 'page', $offset);
            // ProductManager::add_points($paginator,$paginator->items());
           // ProductManager::add_locks($paginator,$paginator->items());
        }
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_featured_products($brand_id , $limit = 100, $offset = 1)
    {
        //change review to ratting
        if (isset($brand_id)) {
            $paginator = Product::with(['rating'])->active()
                ->where('featured', 1)
                ->where('brand_id', '=', $brand_id)
                ->withCount(['order_details'])->orderBy('order_details_count', 'DESC')
                ->paginate($limit, ['*'], 'page', $offset);
               // ProductManager::add_points($paginator,$paginator->items());
        } else {
            $paginator = Product::with(['rating'])->active()
                ->where('featured', 1)
                ->withCount(['order_details'])->orderBy('order_details_count', 'DESC')
                ->paginate($limit, ['*'], 'page', $offset);
                //ProductManager::add_points($paginator,$paginator->items());
        }

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_top_rated_products($brand_id , $limit = 100, $offset = 1)
    {

        if (isset($brand_id)) {
            $reviews = Product::with(['rating'])->active()
                ->where('brand_id', '=', $brand_id)
                ->withCount(['reviews'])->orderBy('reviews_count', 'DESC')
                ->paginate($limit, ['*'], 'page', $offset);

               // ProductManager::add_points($reviews,$reviews->items());
        } else {
            $reviews = Product::with(['rating'])->active()
                ->withCount(['reviews'])->orderBy('reviews_count', 'DESC')
                ->paginate($limit, ['*'], 'page', $offset);
                //ProductManager::add_points($reviews,$reviews->items());
        }

        return [
            'total_size' => $reviews->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $reviews
        ];
    }

    public static function get_best_selling_products($brand_id ,$limit = 100, $offset = 1)
    {
        //change reviews to rattings
        $paginator = OrderDetail::with('product.rating')
            ->whereHas('product', function ($query) {
                $query->active();
            })
            ->select('product_id', DB::raw('COUNT(product_id) as count'))
            ->groupBy('product_id')
            ->orderBy("count", 'desc')
            ->paginate($limit, ['*'], 'page', $offset);

        $data = [];
        $data_id = [];
        foreach ($paginator as $order) {

            if (isset($brand_id)) {
                if ($order->product->brand_id == $brand_id) {
                    array_push($data, $order->product);
                    array_push($data_id, $order->product['id']);
                }
            } else {
                array_push($data, $order->product);
                array_push($data_id, $order->product['id']);
            }
        }

        //Marketing
        $marketing = Marketing::get();
        foreach ($marketing as $market)
        {
            $found=true;
            if ($market->item_type = "product")
            {
                $marketingProduct = Product::active()->with(['rating'])
                    ->where('id', '=', $market->item_id)->get()->first();

                    for($i=0;$i<count($data_id);$i++)
                    {
                         if($market->item_id ==$data_id[$i])
                          $found=false;
                    }

                if (isset($marketingProduct) && $found)
                {
                    array_push($data, $marketingProduct);
                }
            }
        }
        //End Marketing
        // ProductManager::add_points($paginator,$data);

        return [
            'total_size' => count($data),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $data
        ];
    }

    public static function get_related_products($product_id)
    {
        $product = Product::find($product_id);
        return Product::active()->with(['rating'])->where('category_ids', $product->category_ids)
            ->where('id', '!=', $product->id)
            ->limit(10)
            ->get();
    }

    public static function search_products($name, $limit = 20, $offset = 1)
    {
        $key = explode(' ', $name);
        $paginator = Product::active()->with(['rating'])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->paginate($limit, ['*'], 'page', $offset);

      // ProductManager::add_points($paginator,$paginator->items());
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function translated_product_search($name, $limit = 20, $offset = 1)
    {
        $name = base64_decode($name);
        $product_ids = Translation::where('translationable_type', 'App\Model\Product')
            ->where('key', 'name')
            ->where('value', 'like', "%{$name}%")
            ->pluck('translationable_id');

        $paginator = Product::active()->WhereIn('id', $product_ids)->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function product_image_path($image_type)
    {
        $path = '';
        if ($image_type == 'thumbnail') {
            $path = asset('storage/app/public/product/thumbnail');
        } elseif ($image_type == 'product') {
            $path = asset('storage/app/public/product');
        }
        return $path;
    }

    public static function get_product_review($id)
    {
        $reviews = Review::where('product_id', $id)
            ->where('status', 1)->get();
        return $reviews;
    }

    public static function get_rating($reviews)
    {
        $rating5 = 0;
        $rating4 = 0;
        $rating3 = 0;
        $rating2 = 0;
        $rating1 = 0;
        foreach ($reviews as $key => $review) {
            if ($review->rating == 5) {
                $rating5 += 1;
            }
            if ($review->rating == 4) {
                $rating4 += 1;
            }
            if ($review->rating == 3) {
                $rating3 += 1;
            }
            if ($review->rating == 2) {
                $rating2 += 1;
            }
            if ($review->rating == 1) {
                $rating1 += 1;
            }
        }
        return [$rating5, $rating4, $rating3, $rating2, $rating1];
    }

    public static function get_overall_rating($reviews)
    {
        $totalRating = count($reviews);
        $rating = 0;
        foreach ($reviews as $key => $review) {
            $rating += $review->rating;
        }
        if ($totalRating == 0) {
            $overallRating = 0;
        } else {
            $overallRating = number_format($rating / $totalRating, 2);
        }

        return [$overallRating, $totalRating];
    }

    public static function get_shipping_methods($product)
    {
        if ($product['added_by'] == 'seller') {
            $methods = ShippingMethod::where(['creator_id' => $product['user_id']])->where(['status' => 1])->get();
            if ($methods->count() == 0) {
                $methods = ShippingMethod::where(['creator_type' => 'admin'])->where(['status' => 1])->get();
            }
        } else {
            $methods = ShippingMethod::where(['creator_type' => 'admin'])->where(['status' => 1])->get();
        }

        return $methods;
    }

    public static function get_seller_products($seller_id, $limit = 10, $offset = 1)
    {
        $paginator = Product::active()->with(['rating'])
            ->where(['user_id' => $seller_id, 'added_by' => 'seller'])
            ->paginate($limit, ['*'], 'page', $offset);
        /*$paginator->count();*/
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function get_discounted_product($limit = 10, $offset = 1)
    {
        //change review to ratting
        $paginator = Product::with(['rating'])->active()->where('discount', '!=', 0)->latest()->paginate($limit, ['*'], 'page', $offset);
        return [
            'total_size' => $paginator->total(),
            'limit' => (int)$limit,
            'offset' => (int)$offset,
            'products' => $paginator->items()
        ];
    }

    public static function add_points($paginator,$products)
    {
        # code...
        $points = ProductPoint::where('type','product')->get();

        foreach($products as $p){
           foreach($points as $point){

               $idx = json_decode($point->type_id);
               foreach($idx as $d){

                   if($p->id == $d){
                       $p['points'] = $point->points;
                   }
                   else{
                       $p['points'] = '0';
                   }
               }
           }
        }
        return $paginator;
    }

    //Remove bounses when remove product
    public static function remove_bounses($productId)
    {
        $bonuses=Bonus::get();
        foreach($bonuses as $bonus)
        {
            $masterProdNew=[];
            $salveProdNew=[];
            $masterProd=json_decode($bonus->master_product_id);
            $salveProd=json_decode($bonus->salve_product_id);
            for($i=0;$i<count($masterProd);$i++)
            {
                if($masterProd[$i]!=$productId)
                {
                    array_push($masterProdNew,$masterProd[$i]);
                }
            }

            for($i=0;$i<count($salveProd);$i++)
            {
                if($salveProd[$i]!=$productId)
                {
                    array_push($salveProdNew,$salveProd[$i]);
                }
            }

            if(count($masterProdNew)!=0 && count($salveProdNew)!=0)
            {
                $bonus->master_product_id=json_encode($masterProdNew);
                $bonus->salve_product_id=json_encode($salveProdNew);
                $bonus->save();
            }
            else
            {
                Bonus::where('id','=',$bonus->id)->delete();
            }

        }

    }

    //Remove points when remove product
    public static function remove_points($productId)
    {
        $pointProdNew=[];
        $ponits=ProductPoint::get();
        foreach($ponits as $ponit)
        {
            $pointProd=json_decode($ponit->type_id);

            for($i=0;$i<count($pointProd);$i++)
            {
                if($pointProd[$i]!=$productId)
                {
                    array_push($pointProdNew,$pointProd[$i]);
                }
            }

            if(count($pointProdNew)!=0)
            {
                $ponit->type_id=json_encode($pointProdNew);
                $ponit->save();
            }
            else
            {
                ProductPoint::where('id','=',$ponit->id)->delete();
            }

        }

    }


}
