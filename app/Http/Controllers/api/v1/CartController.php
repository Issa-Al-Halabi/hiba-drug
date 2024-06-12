<?php

namespace App\Http\Controllers\api\v1;
use App\CPU\CartManager;
use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Cart;
use App\Model\Product;
use App\Model\Bonus;
use App\Model\ProductsKeys;
use App\Model\Bag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Model\PharmaciesPoints;
use App\Model\RewardItem;

class CartController extends Controller
{

      public function cart(Request $request)
    {
        $user = Helpers::get_customer($request);
        $cart = Cart::where(['customer_id' => $user->id])->get();
        $i = 0;
        foreach ($cart as $c) {

            if ($c->order_type != "bag") {
                $p = Product::whereid($c->product_id)->first();
                if (isset($p)) {
                    $c['q_normal_offer'] = $p->q_normal_offer;
                    $c['q_featured_offer'] = $p->q_featured_offer;
                    $c['normal_offer'] = $p->normal_offer;
                    $c['featured_offer'] = $p->featured_offer;
                    $c['demand_limit'] = (int)$p->demand_limit;
                    $c['locks'] = $p->locks;
                    $c['qty_locks'] = $p->qty_locks;
                } else {
                    $c['q_normal_offer'] = 0;
                    $c['q_featured_offer'] = 0;
                    $c['normal_offer'] = 0;
                    $c['featured_offer'] = 0;
                    $c['demand_limit'] = 0;
                    $c['locks'] = 0;
                    $c['qty_locks'] = 0;
                }
            }
            if ($c->order_type == "bag") {
                $p = Bag::whereid($c->product_id)->first();
                if (isset($p)) {
                    $c['q_normal_offer'] = 0;
                    $c['q_featured_offer'] = 0;
                    $c['normal_offer'] = 0;
                    $c['featured_offer'] = 0;
                    $c['demand_limit'] = (int)$p->demand_limit;
                } else {
                    $c['q_normal_offer'] = 0;
                    $c['q_featured_offer'] = 0;
                    $c['normal_offer'] = 0;
                    $c['featured_offer'] = 0;
                    $c['demand_limit'] = 0;
                }
            }
        }
        $cart->map(function ($data) {
            $data['choices'] = json_decode($data['choices']);
            $data['variations'] = json_decode($data['variations']);
            return $data;
        });

        return response()->json($cart, 200);
    }



    public function add_to_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
            'type' => 'required|string',
        ], [
            'id.required' => translate('Product ID is required!'),
            'type.required' => translate('Type is required!')
        ]);


        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request->type == "product") {
            $validator = Validator::make($request->all(), [
                'pure_price' => 'required',
            ], [
                'pure_price.required' => translate('Pure price is required!')
            ]);
            if ($validator->errors()->count() > 0) {
                return response()->json(['errors' => Helpers::error_processor($validator)]);
            }
        }

        $cart = CartManager::add_to_cart($request);
        return response()->json($cart, 200);
    }



    public function update_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required',
            'quantity' => 'required',
            'type' => 'required',
        ], [
            'key.required' => translate('Cart key or ID is required!'),
            'type.required' => translate('type key is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        if ($request->type == "bag")
            $response = CartManager::bag_update_cart_qty($request);
        else
            $response = CartManager::update_cart_qty($request);

        return response()->json($response);
    }



    public function remove_from_cart(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required'
        ], [
            'key.required' => translate('Cart key or ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $user = Helpers::get_customer($request);



        //Start
        $cart = Cart::where(['id' => $request->key, 'customer_id' => $user->id])->get()->first();
        $ProductKey = ProductsKeys::where([
            ['base_product_id', '=', $cart->product_id],
            ['cus_id', '=', $user->id]
        ])->get()->first();

        if (isset($ProductKey)) {
            $found = ProductsKeys::where('key_id', '=', $ProductKey->key_id)->get(['base_product_id']);
            $objects = Cart::whereIn('product_id', $found)->get();
            if (count($objects) <= 1) {
                $other_products = json_decode($ProductKey->other_product_id, true);
                for ($i = 0; $i < count($other_products); $i++) {
                    $cart = Cart::where(['product_id' => $other_products[$i], 'customer_id' => $user->id])
                        ->update(['update_delete' => 1]);
                }
                ProductsKeys::where([['cus_id', '=', $user->id], ['key_id', '=', $ProductKey->key_id]])->delete();
            }
        }
        //End

      
        // add points if the product is from reward item 
      
      if ($cart->price == -1) {
            $rewardItems = RewardItem::all();
            foreach ($rewardItems as $rewardItem) {
                if ($cart->product_id == $rewardItem->getTypeId()) {
                    $points = $rewardItem->cost * $cart->quantity;
                    PharmaciesPoints::create([
                        "pharmacy_id" => $user->id,
                        "points" => $points,
                        "point_order_id" => $points,
                    ]);
                    break;
                }
            }
        }
        


        Cart::where(['id' => $request->key, 'customer_id' => $user->id])->delete();
        return response()->json(translate('successfully_removed'));
    }




    public function remove_all_from_cart(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'key' => 'required'
        // ], [
        //     'key.required' => translate('Cart key or ID is required!')
        // ]);

        // if ($validator->errors()->count() > 0) {
        //     return response()->json(['errors' => Helpers::error_processor($validator)]);
        // }

        $user = Helpers::get_customer($request);
        Cart::where(['customer_id' => $user->id])->delete();
        ProductsKeys::where('cus_id', '=', $user->id)->delete();
        return response()->json(translate('successfully_removed'));
    }



    public function get_product_keys(Request $request)
    {
        $user = Helpers::get_customer($request);
        $ProductKey = ProductsKeys::where('cus_id', '=', $user->id)->get([
            'id', 'key_id', 'base_product_id'
        ]);
        return response()->json(['status' => true, 'message' => $ProductKey], 200);
    }



    public function order_product_keys(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ], [
            'product_id.required' => translate('Product ID is required!')
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $product_id = $request->product_id;
        $bonus_found = false;
        $other_products = [];
        $other_products_qty = [];
        $base_key = 0;
        $base_products = [];



        //check if bonus found and get other products your link ;
        $bonuses = Bonus::get();
        foreach ($bonuses as $bonus) {
            $base_productsF = json_decode($bonus->salve_product_id, true);

            for ($i = 0; $i < count($base_productsF); $i++) {
                if ((int)$base_productsF[$i] == $product_id) {
                    $bonusObject = $bonus;
                    $bonus_found = true;
                    $other_products = json_decode($bonusObject->master_product_id, true);
                    $other_products_qty = json_decode($bonusObject->master_product_quatity, true);
                    $base_products = json_decode($bonus->salve_product_id, true);
                }
            }
        }


        if ($bonus_found == false) {
            return response()->json(['status' => false, 'message' => 'لا يوجد عرض'], 200);
        }
        //End check ;



        for ($i = 0; $i < count($other_products); $i++) {

            $product_name = Product::whereid($other_products[$i])->first()->only('name', 'id');
            $product_name['quantity'] = $other_products_qty[$i];
            $data_f[] =  $product_name;
        }

        //check if products your link founds in cart ;
        $user = Helpers::get_customer($request);
        for ($i = 0; $i < count($other_products); $i++) {
            $cart = Cart::where([
                ['customer_id', '=', $user->id], ['product_id', '=', $other_products[$i]]
            ])->first();

            if (!isset($cart)) {

                return response()->json(['status' => false, 'details' => ['message' => 'لم يتم ادخال كامل المنتجات الى السلة', 'products' => $data_f]], 200);
            }
            if ($other_products_qty[$i] > $cart->quantity) {
                return response()->json(['status' => false, 'details' => ['message' => 'يرجى ادخال الحد الادنى للمنتجات من اجل امكانية الشراء', 'products' => $data_f]], 200);
            }
        }
        //End check ;



        if ($request->check == 1)
            return response()->json(['status' => true, 'details' => ['products' => $data_f]], 200);
        else {
            //lock update and delete
            for ($i = 0; $i < count($other_products); $i++) {
                $cart = Cart::where([
                    ['customer_id', '=', $user->id], ['product_id', '=', $other_products[$i]]
                ])->first();
                $cart->update_delete = 0;
                $cart->save();
            }
            //End lock


            //Grant key ;
            $base_key = rand(100000, 999999);


            for ($i = 0; $i < count($base_products); $i++) {

                $ProductKey = ProductsKeys::where([['base_product_id', '=', (int)$base_products[$i]], ['cus_id', '=', $user->id]])
                    ->get()->first();
                if (isset($ProductKey)) {
                    $base_key = $ProductKey->key_id;
                } else {
                    $product_key = new ProductsKeys;
                    $product_key->key_id = $base_key;
                    $product_key->cus_id = $user->id;
                    $product_key->base_product_id = (int)$base_products[$i];
                    $product_key->other_product_id = json_encode($other_products);
                    $product_key->save();
                }
            }

            return response()->json(['status' => true, 'details' => ['key' => $base_key, 'products' => $data_f]], 200);
            //End Grant;
        }
    }


    function generateNumber()
    {
        $number = mt_rand(1000000000, 9999999999);
        if ($this->NumberExists($number)) {
            return $this->generateNumber();
        }
        return $number;
    }



    function NumberExists($number)
    {
        return ProductsKeys::where('key_id', '=', $number)->exists();
    }
}
