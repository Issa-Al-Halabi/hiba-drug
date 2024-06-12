<?php

namespace App\CPU;

use App\Model\Admin;
use App\Model\AdminWallet;
use App\Model\Cart;
use App\Model\Order;
use App\Model\Brand;
use App\Model\OrderDetail;
use App\Model\OrderTransaction;
use App\Pharmacy;
use App\Model\Product;
use App\Model\ProductPoint;
use App\Model\Seller;
use App\CPU\Helpers;
use App\Model\PharmaciesPoints;
use App\Model\SellerWallet;
use App\Model\OrdersPoints;
use App\Model\BagsOrdersDetails;
use App\Model\BagProduct;
use App\Services\AlameenSystemServices;
use App\Services\BagServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use App\User;

class OrderManager
{
    public static function track_order($order_id)
    {
        $order = Order::where(['id' => $order_id])->first();
        $order['billing_address_data'] = json_decode($order['billing_address_data']);
        $order['shipping_address_data'] = json_decode($order['shipping_address_data']);
        return $order;
    }

    public static function gen_unique_id()
    {
        return rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
    }

    public static function order_summary($order)
    {
        $sub_total = 0;
        $total_tax = 0;
        $total_discount_on_product = 0;
        $sub_total_bag = 0;
        $total_tax_bag = 0;
        $total_discount_on_product_bag = 0;

        $bagOrderDetails = BagsOrdersDetails::where('order_id', '=', $order->id)->get();

        foreach ($bagOrderDetails as $detail) {
            $sub_total_bag += ($detail->bag_qty * $detail->bag_price);
            $total_tax_bag += $detail->bag_tax;
            $total_discount_on_product_bag += $detail->bag_discount;
        }


        foreach ($order->details as $key => $detail) {
            $sub_total += ($detail->price * $detail->qty);
            $total_tax += $detail->tax;
            $total_discount_on_product += $detail->discount;
        }

        $sub_total = $sub_total + $sub_total_bag;
        $total_tax = $total_tax + $total_tax_bag;
        $total_discount_on_product = $total_discount_on_product + $total_discount_on_product_bag;

        $total_shipping_cost = $order['shipping_cost'];
        return [
            'subtotal' => $sub_total,
            'total_tax' => $total_tax,
            'total_discount_on_product' => $total_discount_on_product,
            'total_shipping_cost' => $total_shipping_cost,
        ];
    }

    public static function stock_update_on_order_status_change($order, $status)
    {
        if ($status == 'returned' || $status == 'failed' || $status == 'canceled') {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 1) {
                    $product = Product::find($detail['product_id']);
                    $type = $detail['variant'];
                    $var_store = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] += $detail['qty'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($var_store),
                        'current_stock' => $product['current_stock'] + $detail['qty'] +  $detail['total_qty'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 0
                    ]);
                }
            }
        } else {
            foreach ($order->details as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $product = Product::find($detail['product_id']);

                    $type = $detail['variant'];
                    $var_store = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] -= $detail['qty'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($var_store),
                        'current_stock' => $product['current_stock'] - $detail['qty'] - $detail['total_qty'],
                    ]);
                    OrderDetail::where(['id' => $detail['id']])->update([
                        'is_stock_decreased' => 1
                    ]);
                }
            }
        }
    }


    public static function stock_update_on_order_delete_change($detail, $order_id)
    {
        $product = Product::find($detail['product_id']);
        $type = $detail['variant'];
        $var_store = [];
        foreach (json_decode($product['variation'], true) as $var) {
            if ($type == $var['type']) {
                $var['qty'] += $detail['qty'];
            }
            array_push($var_store, $var);
        }
        Product::where(['id' => $product['id']])->update([
            'variation' => json_encode($var_store),
            'current_stock' => $product['current_stock'] + $detail['qty'],
        ]);
        $order = Order::where('id', '=', $order_id)->get()->first();
        $order->order_amount = CartManager::order_grand_total($order_id, $detail['product_id'], "delete") - $order->discount;
        $order->save();
    }

    public static function stock_update_on_order_edit_change($detail, $order_id, $qtyNew, $qtyOfferNew,$priceNew)
    {
        $product = Product::find($detail['product_id']);
        $type = $detail['variant'];
        $var_store = [];
        $total_qty = 0;
        $offerType = 'no offer';

        foreach (json_decode($product['variation'], true) as $var) {
            if ($type == $var['type']) {
                $var['qty'] += $qtyNew;
            }
            array_push($var_store, $var);
        }

        //Check if the offer has been modified
        if ($detail['total_qty'] != $qtyOfferNew) {
            $total_qty = $qtyOfferNew;
            $offerType = 'featured';
        } else {
            $total_qty = OrderManager::featured_offer_calculation($product->q_featured_offer, $product->featured_offer, $qtyNew);
            ($total_qty != 0) ? $offerType = 'featured' : $offerType = 'no offer';
            if ($total_qty == 0) {
                $total_qty = OrderManager::normal_offer_calculation($product->q_normal_offer, $product->normal_offer, $qtyNew);
                ($total_qty != 0) ? $offerType = 'normal' : $offerType = 'no offer';
            }
        }
        //ُEnd Check

        Product::where(['id' => $product['id']])->update([
            'variation' => json_encode($var_store),
            'current_stock' => $product['current_stock'] + Helpers::compareTwoQuantity($qtyNew, $detail['qty']) + Helpers::compareTwoQuantity($total_qty, $detail['total_qty']),
        ]);

        $ordersDetails = OrderDetail::where('order_id', '=', $order_id)
            ->where('product_id', '=', $detail['product_id'])
            ->get()
            ->first();

        $ordersDetails->qty = $qtyNew;
        $ordersDetails->total_qty = $total_qty;
        $ordersDetails->offerType = $offerType;
        $ordersDetails->price = $priceNew;
        $ordersDetails->save();

        $order = Order::where('id', '=', $order_id)->get()->first();
        $order->order_amount = CartManager::order_grand_total($order_id, $detail['product_id'], "edit") - $order->discount;
        $order->save();
    }

    public static function wallet_manage_on_order_status_change($order, $received_by)
    {
        $order = Order::find($order['id']);
        $order_summary = OrderManager::order_summary($order);
        $order_amount = $order_summary['subtotal'] - $order_summary['total_discount_on_product'] - $order['discount_amount'];
        $commission = Helpers::sales_commission($order);
        $shipping_model = Helpers::get_business_settings('shipping_method');

        if (AdminWallet::where('admin_id', 1)->first() == false) {
            DB::table('admin_wallets')->insert([
                'admin_id' => 1,
                'withdrawn' => 0,
                'commission_earned' => 0,
                'inhouse_earning' => 0,
                'delivery_charge_earned' => 0,
                'pending_amount' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        if (SellerWallet::where('seller_id', $order['seller_id'])->first() == false) {
            DB::table('seller_wallets')->insert([
                'seller_id' => $order['seller_id'],
                'withdrawn' => 0,
                'commission_given' => 0,
                'total_earning' => 0,
                'pending_withdraw' => 0,
                'delivery_charge_earned' => 0,
                'collected_cash' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if ($order['payment_method'] == 'cash_on_delivery') {
            DB::table('order_transactions')->insert([
                'transaction_id' => OrderManager::gen_unique_id(),
                'customer_id' => $order['customer_id'],
                'seller_id' => $order['seller_id'],
                'seller_is' => $order['seller_is'],
                'order_id' => $order['id'],
                'order_amount' => $order_amount,
                'seller_amount' => $order_amount - $commission,
                'admin_commission' => $commission,
                'received_by' => $received_by,
                'status' => 'disburse',
                'delivery_charge' => $order['shipping_cost'],
                'tax' => $order_summary['total_tax'],
                'delivered_by' => $received_by,
                'payment_method' => $order['payment_method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            if ($shipping_model == 'inhouse_shipping') {
                $wallet->delivery_charge_earned += $order['shipping_cost'];
            }
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                }
                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;
                $wallet->total_tax_collected += $order_summary['total_tax'];

                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                    $wallet->collected_cash += $order['order_amount']; //total order amount
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->save();
            }
        } else {
            $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
            $transaction->status = 'disburse';
            $transaction->save();

            $wallet = AdminWallet::where('admin_id', 1)->first();
            $wallet->commission_earned += $commission;
            $wallet->pending_amount -= $order['order_amount'];
            if ($shipping_model == 'inhouse_shipping') {
                $wallet->delivery_charge_earned += $order['shipping_cost'];
            }
            $wallet->save();

            if ($order['seller_is'] == 'admin') {
                $wallet = AdminWallet::where('admin_id', 1)->first();
                $wallet->inhouse_earning += $order_amount;
                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                }
                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            } else {
                $wallet = SellerWallet::where('seller_id', $order['seller_id'])->first();
                $wallet->commission_given += $commission;

                if ($shipping_model == 'sellerwise_shipping') {
                    $wallet->delivery_charge_earned += $order['shipping_cost'];
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'] + $order['shipping_cost'];
                } else {
                    $wallet->total_earning += ($order_amount - $commission) + $order_summary['total_tax'];
                }

                $wallet->total_tax_collected += $order_summary['total_tax'];
                $wallet->save();
            }
        }
    }

    public static function generate_order($data, $pharmacyId)
    {
        $discount = 0;
        /*
        $order_id = 100000 + Order::all()->count() + 1;
        if (Order::find($order_id)) {
            $order_id = Order::orderBy('id', 'DESC')->first()->id + 1;
        }
        */
        $order_id = Order::orderBy('id', 'DESC')->first()->id + 1;
        $user = Helpers::get_customer($data['request']);
        $cart_group_id = $data['cart_group_id'];
        $seller_data = Cart::where(['cart_group_id' => $cart_group_id])->first();
        $OrderId = (int)$order_id;
        $or = [
            'id' => $order_id,
            'verification_code' => rand(100000, 999999),
            'customer_id' => $user->id,
            'seller_id' => $seller_data->seller_id,
            'seller_is' => $seller_data->seller_is,
            'customer_type' => $user->user_type,
            'payment_status' => $data['payment_status'],
            'order_status' => $data['order_status'],
            'payment_method' => $data['payment_method'],
            'transaction_ref' => $data['transaction_ref'],
            'order_group_id' => $data['order_group_id'],
            'discount_amount' => 0,
            'discount_type' => null,
            'order_amount' => CartManager::cart_grand_total($cart_group_id) - $discount,
            'shipping_cost' => CartManager::get_shipping_cost($data['cart_group_id']),
            'shipping_method_id' => 0,
            'shipping_type' => 'order_wise',
            'created_at' => now(),
            'updated_at' => now(),
        ];
      
        $order_id = DB::table('orders')->insertGetId($or);
        foreach (CartManager::get_cart($data['cart_group_id']) as $c) {
            if ($c->order_type == "bag") {
                $bagProducts=BagServices::getProductsBybag($c['product_id']);
                $bagId = $c->product_id;
                //OrderManager::bags_points($user->id, $bagProducts);//////
                $or_dd = [
                    'order_id' => $OrderId,
                    'bag_id' => $bagId,
                    'seller_id' => $c['seller_id'],
                    'bag_details' => json_encode($bagProducts, true),
                    'bag_qty' => $c['quantity'],
                    'bag_price' => $c['price'],
                    'bag_tax' => $c['tax'] * $c['quantity'],
                    'bag_discount' => $c['discount'] * $c['quantity'],
                    'delivery_status' => 'pending',
                    'payment_status' => 'unpaid',
                    'is_stock_decreased' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
             
                DB::table('bags_orders_details')->insert($or_dd);
              
            } else {

                $product = Product::where(['id' => $c['product_id']])->first();
              	if($product) {if ($c['pure_price'] == 1) {
                    $total_qty = 0;
                    $offerType = 'no offer';
                    $pure_price = 1;
                } else {
                    $total_qty = OrderManager::featured_offer_calculation($product->q_featured_offer, $product->featured_offer, $c['quantity']);
                    ($total_qty != 0) ? $offerType = 'featured' : $offerType = 'no offer';
                    if ($total_qty == 0) {
                        $total_qty = OrderManager::normal_offer_calculation($product->q_normal_offer, $product->normal_offer, $c['quantity']);
                        ($total_qty != 0) ? $offerType = 'normal' : $offerType = 'no offer';
                    }
                    $pure_price = 0;
                }

                $pharmacy = Pharmacy::where('id', $user->id)->get()->first();
                OrderManager::products_points($user->id, $product, $order_id, $c['quantity']);

                $or_d = [
                    'order_id' => $order_id,
                    'product_id' => $c['product_id'],
                    'seller_id' => $c['seller_id'],
                    'product_details' => $product,
                    'qty' => $c['quantity'],
                    'total_qty' => $total_qty,
                    'offerType' => $offerType,
                    'price' => $c['price'],
                    'tax' => $c['tax'] * $c['quantity'],
                    'discount' => $c['discount'] * $c['quantity'],
                    'discount_type' => 'discount_on_product',
                    'variant' => $c['variant'],
                    'variation' => $c['variations'],
                    'delivery_status' => 'pending',
                    'shipping_method_id' => null,
                    'pure_price' => $pure_price,
                    'payment_status' => 'unpaid',
                    'brand_id' => $product->brand_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                if ($c['variant'] != null) {
                    $type = $c['variant'];
                    $var_store = [];
                    foreach (json_decode($product['variation'], true) as $var) {
                        if ($type == $var['type']) {
                            $var['qty'] -= $c['quantity'];
                        }
                        array_push($var_store, $var);
                    }
                    Product::where(['id' => $product['id']])->update([
                        'variation' => json_encode($var_store),
                    ]);
                }

                Product::where(['id' => $product['id']])->update([
                    'current_stock' => $product['current_stock'] - $c['quantity'] - $total_qty
                ]);
                DB::table('order_details')->insert($or_d);
                }
                
            }
          
        }


        if ($or['payment_method'] != 'cash_on_delivery') {
            $order = Order::find($order_id);
            $order_summary = OrderManager::order_summary($order);
            $order_amount = $order_summary['subtotal'] - $order_summary['total_discount_on_product'] - $order['discount'];
            $commission = Helpers::sales_commission($order);

            DB::table('order_transactions')->insert([
                'transaction_id' => OrderManager::gen_unique_id(),
                'customer_id' => $order['customer_id'],
                'seller_id' => $order['seller_id'],
                'seller_is' => $order['seller_is'],
                'order_id' => $order_id,
                'order_amount' => $order_amount,
                'seller_amount' => $order_amount - $commission,
                'admin_commission' => $commission,
                'received_by' => 'admin',
                'status' => 'hold',
                'delivery_charge' => $order['shipping_cost'],
                'tax' => $order_summary['total_tax'],
                'delivered_by' => 'admin',
                'payment_method' => $or['payment_method'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if (AdminWallet::where('admin_id', 1)->first() == false) {
                DB::table('admin_wallets')->insert([
                    'admin_id' => 1,
                    'withdrawn' => 0,
                    'commission_earned' => 0,
                    'inhouse_earning' => 0,
                    'delivery_charge_earned' => 0,
                    'pending_amount' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('admin_wallets')->where('admin_id', $order['seller_id'])->increment('pending_amount', $order['order_amount']);
        }

        if ($seller_data->seller_is == 'admin') {
            $seller = Admin::find($seller_data->seller_id);
        } else {
            $seller = Seller::find($seller_data->seller_id);
        }

        try {

            if ($data['payment_method'] != 'cash_on_delivery') {
                $value = Helpers::order_status_update_message('confirmed');
            } else {
                $value = Helpers::order_status_update_message('pending');
            }

            if ($value) {
                if ($user->user_type == "pharmacist") {
                    $pharmacy_fcm_token = $user->cm_firebase_token;    //الصيدلية
                    $pharmacy_idNew = Pharmacy::where('user_id', '=', $user->id)->get()->first();  //معرف الصيدلية

                    $sales_id  = DB::select('select sales_id from sales_pharmacy where pharmacy_id = ?', [$pharmacy_idNew->id]);
                    $arr = array();
                    foreach ($sales_id as $idx) {
                        array_push($arr, $idx->sales_id);
                    }
                    $salesMans = User::whereIn('id', $arr)->get(); //المندوبين
                    $data = [
                        'title' => translate('order'),
                        'description' => $value,
                        'order_id' => $order_id,
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($pharmacy_fcm_token, $data);
                    foreach ($salesMans as $u) {
                        $dataa = [
                            'title' => translate('order'),
                            'description' => "تم تلقي طلبية من صيدلية : (" . $pharmacy_idNew->name . ")," . $value,
                            'order_id' => $order_id,
                            'image' => '',
                        ];

                        Helpers::send_push_notif_to_device($u->cm_firebase_token, $dataa);
                        Helpers::store_notif_to_db($u->id, $dataa);
                    }
                } else {
                    $salesMan_fcm_token = $user->cm_firebase_token;   //المندوب
                    $pharmacy = Pharmacy::where('id', '=', $pharmacyId)->get()->first();
                    $userR = User::where('id', '=', $pharmacy->user_id)->get()->first();
                    $pharmacy_fcm_token = $userR->cm_firebase_token;   //الصيدلية

                    $data = [
                        'title' => translate('order'),
                        'description' => $value,
                        'order_id' => $order_id,
                        'image' => '',
                    ];
                    $datas = [
                        'title' => translate('order'),
                        'description' => "تم تلقي طلبية من مندوب : (" . $user->name . ")," . $value,
                        'order_id' => $order_id,
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_device($salesMan_fcm_token, $data);
                    Helpers::send_push_notif_to_device($pharmacy_fcm_token, $datas);
                    Helpers::store_notif_to_db($userR->id, $datas);
                }
            }
        } catch (\Exception $exception) {
          
        }
          
        return $order_id;
    }

/*
   public function bags_points($pharmacy_id, $bags)
    {
        # code...
        $points = 0;
        $productpoint = ProductPoint::wheretype('bag')->get();
      	$pharmacy_points_id = Pharmacy::where('user_id', $pharmacy_id)->first();///
        $id_of_pharmacy=$pharmacy_points_id ->id;////
        foreach ($productpoint as $p) {

            foreach ($bags as $product) {

                $idx = json_decode($p->type_id);
                if (in_array($product->id, $idx)) {
                    $points = $points + $p->points;
                }
            }
        }
        if ($points != 0) {
            $pharmacy = PharmaciesPoints::where('pharmacy_id', $pharmacy_id)->first();
            if (isset($pharmacy)) {
                $pharmacy->points = $pharmacy->points + $points;
                $pharmacy->save();
            } else {
                $pharmacy_points = new PharmaciesPoints();
                $pharmacy_points->pharmacy_id = $pharmacy_id;
              	$pharmacy_points->id_of_pharmacy = $id_of_pharmacy;/////
                $pharmacy_points->points = $points;
                $pharmacy_points->save();
            }
        }
        return $points;
    }
*/
/*
      public function bags_points($pharmacy_id, $bags)
    {
        # code...
        $points = 0;
        $productpoint = ProductPoint::wheretype('bag')->get();
        $pharmacy_points_id = User::where('id', $pharmacy_id)->first();///
        $userType=$pharmacy_points_id ->user_type;////
        if ($userType == "pharmacist") {
        	$pharmacy_points_id = Pharmacy::where('user_id', $pharmacy_id)->first();///
             $id_of_pharmacy=$pharmacy_points_id ->id;////
            foreach ($productpoint as $p) {

                foreach ($bags as $product) {

                    $idx = json_decode($p->type_id);
                    if (in_array($product->id, $idx)) {
                        $points = $points + $p->points;
                    }
                }
            }
            if ($points != 0) {
                $pharmacy = PharmaciesPoints::where('pharmacy_id', $pharmacy_id)->first();
                if (isset($pharmacy)) {
                    $pharmacy->points = $pharmacy->points + $points;
                    $pharmacy->save();
                } else {
                    $pharmacy_points = new PharmaciesPoints();
                    $pharmacy_points->pharmacy_id = $pharmacy_id;
                	$pharmacy_points->id_of_pharmacy = $id_of_pharmacy;/////
                    $pharmacy_points->points = $points;
                    $pharmacy_points->save();
                }
            }
        }
        return $points;
    }
*/
public function bags_points($pharmacy_id, $bags)
{
    $points = 0;
    $productpoints = ProductPoint::where('type', 'bag')->get();
    $pharmacy = Pharmacy::where('user_id', $pharmacy_id)->first();
    
    if ($pharmacy && $pharmacy->user_type == "pharmacist") {
        foreach ($productpoints as $productpoint) {
            $idx = json_decode($productpoint->type_id);
            foreach ($bags as $bag) {
                if (in_array($bag->id, $idx)) {
                    $points += $productpoint->points;
                }
            }
        }

        if ($points > 0) {
            $pharmacy_points = PharmaciesPoints::where('pharmacy_id', $pharmacy->id)->first();
            if ($pharmacy_points) {
                $pharmacy_points->points += $points;
                $pharmacy_points->save();
            } else {
                $pharmacy_points = new PharmaciesPoints();
                $pharmacy_points->pharmacy_id = $pharmacy->id;
                $pharmacy_points->points = $points;
                $pharmacy_points->save();
            }
        }
    }
    return $points;
}



    public function order_points($pharmacy_id, $order_total_price)
    {

        # code...
        $points = 0;
        $orderpoint = OrdersPoints::get();
       	//$pharmacy_points_id = Pharmacy::where('user_id', $pharmacy_id)->first();///
        //$id_of_pharmacy=$pharmacy_points_id ->id;////
        foreach ($orderpoint as $p) {


            if ($order_total_price >= $p->points) {
                $points = $points + $p->points;
            }
        }
        if ($points != 0) {
            $pharmacy = PharmaciesPoints::where('pharmacy_id', $pharmacy_id)->first();
            if (isset($pharmacy)) {
                $pharmacy->points = $pharmacy->points + $points;
                $pharmacy->save();
            } else {
                $pharmacy_points = new PharmaciesPoints();
                $pharmacy_points->pharmacy_id = $pharmacy_id;
              	//$pharmacy_points->id_of_pharmacy = $id_of_pharmacy;/////
                $pharmacy_points->points = $points;
                $pharmacy_points->save();
            }
        }
        return $points;
    }


    public static function stock_update_on_bag_order_status_change($order, $status)
    {
        if ($status == 'returned' || $status == 'failed' || $status == 'canceled') {

            $bagDetails = BagsOrdersDetails::where('order_id', '=', $order->id)->get();

            foreach ($bagDetails as $detail) {
                if ($detail['is_stock_decreased'] == 1) {

                    $bagProducts = json_decode($detail->bag_details, true);

                    foreach ($bagProducts as $bagProduct) {

                        $product = Product::where('id', '=', $bagProduct['product_id'])->get()->first();

                        Product::where(['id' => $product['id']])->update([
                            'current_stock' => $product['current_stock'] + $bagProduct['product_count'] * $detail['bag_qty'],
                        ]);

                        BagsOrdersDetails::where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 0
                        ]);
                    }
                }
            }
        } else {
            $bagDetails = BagsOrdersDetails::where('order_id', '=', $order->id)->get();

            foreach ($bagDetails as $detail) {
                if ($detail['is_stock_decreased'] == 0) {
                    $bagProducts = json_decode($detail->bag_details, true);
                    foreach ($bagProducts as $bagProduct) {

                        $product = Product::where('id', '=', $bagProduct['product_id'])->get()->first();
                        Product::where(['id' => $product['id']])->update([
                            'current_stock' => $product['current_stock'] - $bagProduct['product_count'] * $detail['bag_qty'],
                        ]);
                        BagsOrdersDetails::where(['id' => $detail['id']])->update([
                            'is_stock_decreased' => 1
                        ]);
                    }
                }
            }
        }
    }



       public static  function products_points($pharmacy_id, $product, $order_id, $product_quantity)
    {

        $pharmacy_points_id = Order::where('id', $order_id)->first();///
        $userType=$pharmacy_points_id ->customer_type;////
        $points = 0;
        if ($userType == "pharmacist") {
          
            $points = 0;
            $productpoint = ProductPoint::wheretype('product')->get();
            $pharmacy_po_id = Pharmacy::where('user_id', $pharmacy_id)->first();///
        	$id_of_pharmacy=$pharmacy_po_id ->id;///
            foreach ($productpoint as $p) {

                $quantity = (int)($product_quantity / $p->quantity);

                $idx = json_decode($p->type_id);
                if (in_array($product->id, $idx)) {
                    $points = $points + $p->points;
                }
            }

            if ($points != 0) {
                $points = $points * $quantity;
                $pharmacy_points = new PharmaciesPoints();
                $pharmacy_points->points = $pharmacy_points->points + $points;
                $pharmacy_points->pharmacy_id = $pharmacy_id;
                $pharmacy_points->id_of_pharmacy = $id_of_pharmacy;
                $pharmacy_points->point_order_id = $order_id;
                $pharmacy_points->points = $points;
                $pharmacy_points->save();
            }

        }
        
        return $points;
    }




    public static function getPharmacyName($userType, $id)
    {
      
        if ($userType == "salesman") {
            $details = Pharmacy::where('id', '=', $id)->get()->first();
            if (isset($details))
                return $details->name;
        } else {
            $details = Pharmacy::where('user_id', '=', $id)->get()->first();
            if (isset($details))
                return $details->name;
        }
        return " ";
    }


    public static function featured_offer_calculation($q_featured_offer, $featured_offer, $qty)
    {
        $total_qty = 0;
        if ($q_featured_offer != 0 &&  $featured_offer != 0) {
            $total_qty = ((int)($qty / $q_featured_offer)) * $featured_offer;
        }
        return  $total_qty;
    }

    public static function normal_offer_calculation($q_normal_offer, $normal_offer, $qty)
    {
        $total_qty = 0;
        if ($q_normal_offer != 0 && $normal_offer != 0) {
            $total_qty = ((int)($qty / $q_normal_offer)) * $normal_offer;
        }
        return  $total_qty;
    }


    public static function sendOrderToAlameenSystem($order_id, $orderStatus)
    {
        try {
            $order = Order::where('id', '=', $order_id)->get()->first();
            $objectSystem = new AlameenSystemServices();
            if ($orderStatus == "confirmed" && $order->status_export == 0)
                if ($objectSystem->storeOrder($order_id))
                    $order->status_export = 1;
            $order->save();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}