<?php

namespace App\CPU;

use App\Model\Cart;
use App\Model\CartShipping;
use App\Model\Color;
use App\Model\OrderDetail;
use App\Model\Product;
use App\Model\Bonus;
use App\Model\Bag;
use App\Model\BagProduct;
use App\Model\Shop;
use App\Model\ProductsKeys;
use Barryvdh\Debugbar\Twig\Extension\Debug;
use Cassandra\Collection;
use Illuminate\Support\Str;
use App\Model\ShippingType;
use App\Model\CategoryShippingCost;

class CartManager
{
    public static function cart_to_db()
    {
        $user = Helpers::get_customer();
        if (session()->has('offline_cart')) {
            $cart = session('offline_cart');
            $storage = [];
            foreach ($cart as $item) {
                $db_cart = Cart::where(['customer_id' => $user->id, 'seller_id' => $item['seller_id'], 'seller_is' => $item['seller_is']])->first();
                $storage[] = [
                    'customer_id' => $user->id,
                    'cart_group_id' => isset($db_cart) ? $db_cart['cart_group_id'] : str_replace('offline', $user->id, $item['cart_group_id']),
                    'product_id' => $item['product_id'],
                    'color' => $item['color'],
                    'choices' => $item['choices'],
                    'variations' => $item['variations'],
                    'variant' => $item['variant'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'tax' => $item['tax'],
                    'discount' => $item['discount'],
                    'slug' => $item['slug'],
                    'name' => $item['name'],
                    'thumbnail' => $item['thumbnail'],
                    'seller_id' => ($item['seller_is'] == 'admin') ? 1 : $item['seller_id'],
                    'seller_is' => $item['seller_is'],
                    'shop_info' => $item['shop_info'],
                    'shipping_cost' => $item['shipping_cost'],
                    'shipping_type' => $item['shipping_type'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Cart::insert($storage);
            session()->put('offline_cart', collect([]));
        }
    }

    public static function get_cart($group_id = null)
    {
        if ($group_id == null) {
            $cart = Cart::whereIn('cart_group_id', CartManager::get_cart_group_ids())->get();
        } else {
            $cart = Cart::where('cart_group_id', $group_id)->get();
        }
        return $cart;
    }

    public static function get_cart_group_ids($request = null)
    {
        // $user = Helpers::get_customer($request);
        // $cart_ids = Cart::where(['customer_id' => $user->id])->groupBy('cart_group_id')->pluck('cart_group_id')->toArray();
        // return $cart_ids;

        $user = Helpers::get_customer($request);
        $cart_ids = Cart::where(['customer_id' => $user->id])->get(['cart_group_id'])->first();
        return $cart_ids;
    }

    public static function get_shipping_cost($group_id = null)
    {
        $cost = 0;
        if ($group_id == null) {
            $order_wise_shipping_cost = CartShipping::whereIn('cart_group_id', CartManager::get_cart_group_ids())->sum('shipping_cost');
            $cart_shipping_cost = Cart::whereIn('cart_group_id', CartManager::get_cart_group_ids())->sum('shipping_cost');
            $cost = $order_wise_shipping_cost + $cart_shipping_cost;
        } else {
            $data = CartShipping::where('cart_group_id', $group_id)->first();
            $order_wise_shipping_cost = isset($data) ? $data->shipping_cost : 0;
            $cart_shipping_cost = Cart::where('cart_group_id', $group_id)->sum('shipping_cost');
            $cost = $order_wise_shipping_cost + $cart_shipping_cost;
        }
        return $cost;
    }

    public static function cart_total($cart)
    {
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product_subtotal = $item['price'] * $item['quantity'];
                $total += $product_subtotal;
            }
        }
        return $total;
    }

    public static function cart_total_applied_discount($cart)
    {
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product_subtotal = ($item['price'] - $item['discount']) * $item['quantity'];
                $total += $product_subtotal;
            }
        }
        return $total;
    }

    public static function cart_total_with_tax($cart)
    {
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product_subtotal = ($item['price'] * $item['quantity']) + ($item['tax'] * $item['quantity']);
                $total += $product_subtotal;
            }
        }
        return $total;
    }

    public static function cart_grand_total($cart_group_id = null)
    {
        $cart = CartManager::get_cart($cart_group_id);
        $shipping_cost = CartManager::get_shipping_cost($cart_group_id);
        $total = 0;
        if (!empty($cart)) {
            foreach ($cart as $item) {
                $product_subtotal = ($item['price'] * $item['quantity'])
                    + ($item['tax'] * $item['quantity'])
                    - $item['discount'] * $item['quantity'];
                $total += $product_subtotal;
            }
            $total += $shipping_cost;
        }
        return $total;
    }

    public static function order_grand_total($order_id, $product_id, $status)
    {
        $ordersDetails = OrderDetail::where('order_id', '=', $order_id)->get();
        // $shipping_cost = CartManager::get_shipping_cost($cart_group_id);
        $total = 0;
        if (!empty($ordersDetails)) {
            foreach ($ordersDetails as $item) {
                if ($status == "delete") {
                    if ($item->product_id != $product_id) {
                        $product_subtotal = ($item['price'] * $item['qty'])
                            + ($item['tax'] * $item['qty'])
                            - $item['discount'] * $item['qty'];
                        $total += $product_subtotal;
                    }
                } else {
                    $product_subtotal = ($item['price'] * $item['qty'])
                        + ($item['tax'] * $item['qty'])
                        - $item['discount'] * $item['qty'];
                    $total += $product_subtotal;
                }
            }
            // $total += $shipping_cost;
        }
        return $total;
    }

    public static function cart_clean($request = null)
    {
        $cart_ids = CartManager::get_cart_group_ids($request);
        CartShipping::whereIn('cart_group_id', $cart_ids)->delete();
        Cart::whereIn('cart_group_id', $cart_ids)->delete();

        session()->forget('coupon_code');
        session()->forget('coupon_discount');
        session()->forget('payment_method');
        session()->forget('shipping_method_id');
        session()->forget('billing_address_id');
        session()->forget('order_id');
        session()->forget('cart_group_id');
        session()->forget('order_note');
    }

    public static function add_to_cart($request, $from_api = false)
    {
        $str = '';
        $variations = [];
        $choices = [];
        $price = 0;

        $user = Helpers::get_customer($request);
        if ($request->type == "product") {
            $product = Product::find($request->id);

            //Start
            $bonuses = Bonus::get();
            foreach ($bonuses as $bonus) {
                $base_products = json_decode($bonus->salve_product_id, true);
                $other_products = json_decode($bonus->master_product_id, true);
                $other_products_qty = json_decode($bonus->master_product_quatity, true);

                for ($i = 0; $i < count($base_products); $i++) {
                    if ($base_products[$i] == $product->id) {
                        if ($request->product_key) {
                            $ProductKey = ProductsKeys::where([
                                ['base_product_id', '=', $product->id],
                                ['key_id', '=', $request->product_key],
                                ['cus_id', '=', $user->id]
                            ])->get()->first();
                            if (!isset($ProductKey))
                                return response()->json(['status' => false, 'message' => 'مفتاح المنتج غير صحيح']);
                        } else {
                            return response()->json(['status' => false, 'message' => '( مفتاح المنتج مطلوب ) المنتج مقفول']);
                        }

                        break;
                    }
                }
            }
            //End

        } elseif ($request->type == "bag") {
            $bag = Bag::find($request->id);
            if ($bag) {
                return CartManager::bag_add_to_cart($request, $user, $bag);
            } else {
                return [
                    'status' => 0,
                    'message' => translate('Bag not found!')
                ];
            }
        } else {
            return [
                'status' => 0,
                'message' => translate('Error!')
            ];
        }

        if ($product) {
            //chek if product added
            $CheckProduct = CartManager::checkProductAdded($request->id, $user->id);
            if ($CheckProduct) {
                return [
                    'status' => 0,
                    'message' => translate('already_added!')
                ];
            } else {
                $cart = new Cart();
            }

            //chek if out of stock
            if ($product['current_stock'] < $request['quantity']) {
                return [
                    'status' => 0,
                    'message' => translate('out_of_stock!')
                ];
            }

            $cart['color'] = $request->has('color') ? $request['color'] : null;
            $cart['product_id'] = $product->id;
            $cart['choices'] = json_encode($choices);
            $cart['variations'] = json_encode($variations);
            $cart['variant'] = $str;


            $price = $product->unit_price;
            $tax = Helpers::tax_calculation($price, $product['tax'], 'percent');

            //generate group id
            $cart_check = Cart::where([
                'customer_id' => $user->id,
                'seller_id' => ($product->added_by == 'admin') ? 1 : $product->user_id,
                'seller_is' => $product->added_by
            ])->first();


            if (isset($cart_check)) {
                $cart['cart_group_id'] = $cart_check['cart_group_id'];
            } else {
                $cart['cart_group_id'] = ($user == 'offline' ? 'offline' : $user->id) . '-' . Str::random(5) . '-' . time();
            }
            //generate group id end

            $cart['customer_id'] = $user->id ?? 0;
            $cart['quantity'] = $request['quantity'];


            //calculation pure price
            if ($request['pure_price'] != 1) {
                $cart['price'] = $price;
                $cart['pure_price'] = 0;
            } else {
                if ($product->q_normal_offer != 0 && $product->normal_offer != 0) {
                    $total_qty = 0;
                    $total_qty = ((int)($request['quantity'] / $product->q_normal_offer)) * $product->normal_offer;
                    $cart['price'] = CartManager::pure_price_calculation($price, $total_qty, $request['quantity']);
                    $cart['pure_price'] = 1;
                } else {
                    $cart['price'] = $price;
                    $cart['pure_price'] = 0;
                }
            }
            //end calculation pure price

            $cart['tax'] = $tax;
            $cart['slug'] = $product->slug;
            $cart['name'] = $product->name;
            $cart['discount'] = Helpers::get_product_discount($product, $price);
            $cart['thumbnail'] = $product->thumbnail;
            $cart['seller_id'] = ($product->added_by == 'admin') ? 1 : $product->user_id;
            $cart['seller_is'] = $product->added_by;
            $cart['shipping_cost'] = CartManager::get_shipping_cost_for_product_category_wise($product, $request['quantity']);
            if ($product->added_by == 'seller') {
                $cart['shop_info'] = Shop::where(['seller_id' => $product->user_id])->first()->name;
            } else {
                $cart['shop_info'] = Helpers::get_business_settings('company_name');
            }

            $shippingMethod = Helpers::get_business_settings('shipping_method');

            if ($shippingMethod == 'inhouse_shipping') {
                $admin_shipping = ShippingType::where('seller_id', 0)->first();
                $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
            } else {
                if ($product->added_by == 'admin') {
                    $admin_shipping = ShippingType::where('seller_id', 0)->first();
                    $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
                } else {
                    $seller_shipping = ShippingType::where('seller_id', $product->user_id)->first();
                    $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
                }
            }
            $cart['shipping_type'] = $shipping_type;
            $cart->save();

            return [
                'status' => 1,
                'message' => translate('successfully_added!')
            ];
        } else {
            return [
                'status' => 0,
                'message' => translate('Product not found!')
            ];
        }
    }


    public static function update_cart_qty($request)
    {
        $user = Helpers::get_customer($request);
        $status = 1;
        $qty = 0;
        $cart = Cart::where(['id' => $request->key, 'customer_id' => $user->id])->first();

        $product = Product::find($cart['product_id']);

        //Start
        $bonuses = Bonus::get();
        foreach ($bonuses as $bonus) {
            $base_products = json_decode($bonus->salve_product_id, true);
            $base_products_qty = json_decode($bonus->salve_product_quatity, true);
            for ($i = 0; $i < count($base_products); $i++) {
                if ($base_products[$i] == $product->id) {
                    if ($base_products_qty[$i] < $request->quantity) {
                        return response()->json(['status' => false, 'message' => 'لم يتم تعديل الكمية المطلوبة لانه يجب تحقيق الحد الادنى من الكمية لتحقيق العرض '], 200);
                    }
                    break;
                }
            }
        }
        //End

        $count = count(json_decode($product->variation));
        if ($count) {
            for ($i = 0; $i < $count; $i++) {
                if (json_decode($product->variation)[$i]->type == $cart['variant']) {
                    if (json_decode($product->variation)[$i]->qty < $request->quantity) {
                        $status = 0;
                        $qty = $cart['quantity'];
                    }
                }
            }
        } else if ($product['current_stock'] < $request->quantity) {
            $status = 0;
            $qty = $cart['quantity'];
        }

        if ($status) {
            $qty = $request->quantity;
            //Cal Pure Price
            if ($cart['pure_price'] != 1) {
                $cart['price'] =  $product->unit_price;
            } else {
                if ($product->q_normal_offer != 0 && $product->normal_offer != 0) {
                    $price = $product->unit_price;
                    $total_qty = ((int)($request['quantity'] / $product->q_normal_offer)) * $product->normal_offer;
                    $pure_price_new = CartManager::pure_price_calculation($price, $total_qty, $request['quantity']);
                    $cart['price'] = $pure_price_new;
                    if ($total_qty != 0) {
                        $cart['pure_price'] = 1;
                    } else {
                        $cart['pure_price'] = 0;
                    }
                }
            }

            //End Cal Pure Price
            $cart['quantity'] = $request->quantity;
            $cart['shipping_cost'] =  CartManager::get_shipping_cost_for_product_category_wise($product, $request->quantity);
        }

        $cart->save();

        return [
            'status' => $status,
            'qty' => $qty,
            'message' => $status == 1 ? translate('successfully_updated!') : translate('sorry_stock_is_limited')
        ];
    }

    public static function get_shipping_cost_for_product_category_wise($product, $qty)
    {
        $shippingMethod = Helpers::get_business_settings('shipping_method');
        $cost = 0;

        if ($shippingMethod == 'inhouse_shipping') {
            $admin_shipping = ShippingType::where('seller_id', 0)->first();
            $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
        } else {
            if ($product->added_by == 'admin') {
                $admin_shipping = ShippingType::where('seller_id', 0)->first();
                $shipping_type = isset($admin_shipping) == true ? $admin_shipping->shipping_type : 'order_wise';
            } else {
                $seller_shipping = ShippingType::where('seller_id', $product->user_id)->first();
                $shipping_type = isset($seller_shipping) == true ? $seller_shipping->shipping_type : 'order_wise';
            }
        }

        if ($shipping_type == 'category_wise') {
            $categoryID = 0;
            foreach (json_decode($product->category_ids) as $ct) {
                if ($ct->position == 1) {
                    $categoryID = $ct->id;
                }
            }

            if ($shippingMethod == 'inhouse_shipping') {
                $category_shipping_cost = CategoryShippingCost::where('seller_id', 0)->where('category_id', $categoryID)->first();
            } else {
                if ($product->added_by == 'admin') {
                    $category_shipping_cost = CategoryShippingCost::where('seller_id', 0)->where('category_id', $categoryID)->first();
                } else {
                    $category_shipping_cost = CategoryShippingCost::where('seller_id', $product->user_id)->where('category_id', $categoryID)->first();
                }
            }

            if ($category_shipping_cost->multiply_qty == 1) {
                $cost = $qty * $category_shipping_cost->cost;
            } else {
                $cost = $category_shipping_cost->cost;
            }
        } else if ($shipping_type == 'product_wise') {

            if ($product->multiply_qty == 1) {
                $cost = $qty * $product->shipping_cost;
            } else {
                $cost = $product->shipping_cost;
            }
        } else {
            $cost = 0;
        }

        return $cost;
    }

    //Cart bags
    public static function bag_add_to_cart($request, $user, $bag)
    {

        $str = '';
        $variations = [];
        $price = 0;
        $choices = [];

        $cart = Cart::where(['product_id' => $request->id, 'customer_id' => $user->id, 'variant' => $str])->where('order_type', '=', $request->type)->first();
        if (isset($cart) == false) {
            $cart = new Cart();
        } else {
            return [
                'status' => 0,
                'message' => translate('already_added!')
            ];
        }

        $cart['color'] = $request->has('color') ? $request['color'] : null;
        $cart['product_id'] = $bag->id;
        $cart['choices'] = json_encode($choices);

        if ($bag['demand_limit'] < $request['quantity']) {
            return [
                'status' => 0,
                'message' => translate('out_of_demand_limit!')
            ];
        }

        $bagProducts = BagProduct::where('bag_id', '=', $bag->id)->get();
        foreach ($bagProducts as $bagProduct) {
            $product = Product::where('id', '=', $bagProduct->product_id)->get()->first();
            if ($product['current_stock'] < $bagProduct['product_count'] * $request['quantity']) {
                return [
                    'status' => 2,
                    'message' => $product->name . ' : نفذ من المخزن'
                ];
            }
        }

        $cart['variations'] = json_encode($variations);
        $cart['variant'] = $str;
        $price = $bag->total_price_offer;
        $tax = 0;


        $cart_check = Cart::where([
            'customer_id' => $user->id,
            'seller_id' => 1,
            'seller_is' => 'admin'
        ])->first();


        if (isset($cart_check)) {
            $cart['cart_group_id'] = $cart_check['cart_group_id'];
        } else {
            $cart['cart_group_id'] = ($user == 'offline' ? 'offline' : $user->id) . '-' . Str::random(5) . '-' . time();
        }


        $cart['customer_id'] = $user->id ?? 0;
        $cart['quantity'] = $request['quantity'];
        /*$data['shipping_method_id'] = $shipping_id;*/
        $cart['price'] = $price;
        $cart['tax'] = $tax;
        $cart['slug'] = Str::slug($bag->bag_name, '-');
        $cart['name'] = $bag->bag_name;
        $cart['discount'] = 0;
        /*$data['shipping_cost'] = $shipping_cost;*/
        $cart['thumbnail'] = $bag->bag_image;
        $cart['seller_id'] = 1;
        $cart['seller_is'] = 'admin';
        $cart['shipping_cost'] = 0;
        $cart['shop_info'] = Helpers::get_business_settings('company_name');
        $shippingMethod = Helpers::get_business_settings('shipping_method');
        $cart['shipping_type'] = "order_wise";
        $cart['order_type'] = "bag";
        $cart->save();
        return [
            'status' => 1,
            'message' => translate('successfully_added!')
        ];
    }

    public static function bag_update_cart_qty($request)
    {
        $user = Helpers::get_customer($request);
        $status = 1;
        $qty = 0;
        $cart = Cart::where(['id' => $request->key, 'customer_id' => $user->id])->first();
        $bag = Bag::find($cart['product_id']);

        if ($bag['demand_limit'] < $request['quantity']) {
            return [
                'status' => 0,
                'message' => translate('out_of_demand_limit!')
            ];
        }

        $bagProducts = BagProduct::where('bag_id', '=', $bag->id)->get();
        foreach ($bagProducts as $bagProduct) {
            $product = Product::where('id', '=', $bagProduct->product_id)->get()->first();
            if ($product['current_stock'] < $bagProduct['product_count'] * $request['quantity']) {
                $status = 0;
                $qty = $cart['quantity'];
                return [
                    'status' => 0,
                    'message' => translate($product->name . ' : out_of_stock!')
                ];
            }
        }

        if ($status) {
            $qty = $request->quantity;
            $cart['quantity'] = $request->quantity;
            $cart['shipping_cost'] =  CartManager::get_shipping_cost_for_product_category_wise($product, $request->quantity);
        }
        $cart->save();

        return [
            'status' => $status,
            'qty' => $qty,
            'message' => $status == 1 ? translate('successfully_updated!') : translate('sorry_stock_is_limited')
        ];
    }


    public static function pure_price_calculation($price, $offer, $qty)
    {
        if ($offer != 0) {
            $pure_price = ($price * $qty) / ($qty + $offer);
            $pure_price = round($pure_price, -1);
        } else {
            $pure_price = $price;
        }
        return $pure_price;
    }


    public static function checkProductAdded($productId, $UserId)
    {
        $cart = Cart::where(['product_id' => $productId, 'customer_id' => $UserId])->first();
        if (isset($cart)) {
            return true;
        } else {
            return false;
        }
    }
}
