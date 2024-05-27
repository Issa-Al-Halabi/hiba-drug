<?php

namespace App\Http\Controllers\api\v1;
use Illuminate\Support\Facades\Artisan;
use App\CPU\BrandManager;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use App\Model\Category;


class BrandController extends Controller
{
    public function get_brands()
    {
      
      
        try {
            $brands = BrandManager::get_brands()->makeHidden(
                [
                    'updated_at', 'created_at', 'deleted_at', 'shipping'
                ]
            );
        } catch (\Exception $e) {
        }
        return response()->json($brands, 200);
    }

    //public function get_products($brand_id)
    //{
     //   try {
     //       $products = BrandManager::get_products($brand_id);
      //  } catch (\Exception $e) {
     //       return response()->json(['errors' => $e], 403);
      //  }
     //   return response()->json($products, 200);
   // }
  // new
    public function get_products($brand_id)
    {
        try {
            $products = BrandManager::get_products($brand_id);

            // Loop through products and extract category_ids
            foreach ($products as &$product) {
              if (isset($product['category_ids'])) {
                  $category_ids = json_decode($product['category_ids'], true);
                  $ids = array_column($category_ids, 'id');

                  // Query categories using whereIn
                  $categories = Category::whereIn('id', $ids)->get();

                  // Process categories
                  foreach ($categories as $category) {
                      $category_names = $category->name; // Use [] to append to the array
                  }

                  // If category names are empty, assign "_"
                  if (empty($category_names)) {
                      $category_names = "_";
                  }

                  // Add category names to product
                  $product['category_names'] = $category_names;
              }
          }


            // Return the products with category names
            return response()->json($products, 200);

        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }
    // new
}
