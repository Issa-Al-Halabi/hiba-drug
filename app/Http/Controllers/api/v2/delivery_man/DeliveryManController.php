<?php

namespace App\Http\Controllers\api\v2\delivery_man;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\DeliveryHistory;
use App\Model\DeliveryMan;
use App\Model\BagsOrdersDetails;
use App\Model\Order;
use App\Pharmacy;
use App\User;
use App\Model\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\CPU\OrderManager;

class DeliveryManController extends Controller
{

    public function info(Request $request)
    {
        return response()->json($request['delivery_man'], 200);
    }


    public function get_current_orders(Request $request)
    {

        $d_man = $request['delivery_man'];
      
        $orders = Order::whereIn('order_status', ['processing', 'out_for_delivery', 'confirmed'])
            ->where(['delivery_man_id' => $d_man['id']])
            //->where('scheduling', '=', 1)
            ->get()
          
            ->makeHidden(
                [
                    'transaction_ref', 'discount_amount', 'discount_type',
                    'coupon_code', 'shipping_method_id', 'shipping_cost', 'seller_id',
                    'seller_is', 'delivery_man_id', 'billing_address', 'billing_address_data',
                    'extra_discount', 'extra_discount_type', 'shipping_type', 'delivery_service_name', 'Detection_number', 'shipping_address_data',
                    'delivery_type', 'status_export', 'scheduling', 'third_party_delivery_tracking_id', 'pharmacy_id', 'orderBy_id', 'customer_type'
                ]
            );
        foreach ($orders as $order) {
            if ($order->customer_type == "pharmacist") {
                $pharmacy = Pharmacy::where('user_id', '=', $order->customer_id)->get()->first();

                $user = User::where('id', '=', $pharmacy->user_id)->get()->first();

                $order['pharmacy_name'] = $pharmacy->name;
                $order['lat'] = $pharmacy->lat;
                $order['lan'] = $pharmacy->lan;
                $order['phone'] = $user->phone;
                $order['address']=$pharmacy->city."/".$pharmacy->region."/".$pharmacy->Address;
            } else {
                $pharmacy = Pharmacy::where('id', '=', $order->orderBy_id)->get()->first();
                $user = User::where('id', '=', $pharmacy->user_id)->get()->first();
                $order['pharmacy_name'] = $pharmacy->name;
                $order['lat'] = $pharmacy->lat;
                $order['lan'] = $pharmacy->lan;
                $order['phone'] = $user->phone;
                $order['address']=$pharmacy->city."/".$pharmacy->region."/".$pharmacy->Address;
            }
        }

        return response()->json($orders, 200);
    }



    public function record_location_data(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $d_man = $request['delivery_man'];
        DB::table('delivery_histories')->insert([
            'order_id' => $request['order_id'],
            'deliveryman_id' => $d_man['id'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'time' => now(),
            'location' => $request['location'],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        return response()->json(['message' => 'location recorded'], 200);
    }



    public function get_order_history(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $d_man = $request['delivery_man'];
        $history = DeliveryHistory::where(['order_id' => $request['order_id'], 'deliveryman_id' => $d_man['id']])->get();
        return response()->json($history, 200);
    }



    public function update_order_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status' => 'required|in:delivered,canceled,returned,out_for_delivery',
            'order_note_price' => 'nullable',
			'order_receive_price' =>'nullable'
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $d_man = $request['delivery_man'];

        Order::where(['id' => $request['order_id'], 'delivery_man_id' => $d_man['id']])->update([
            'order_status' => $request['status'],
            'order_note_price' => $request['order_note_price'],
            'order_receive_price' => $request['order_receive_price']
        ]);

        $order = Order::where(['delivery_man_id' => $d_man['id'], 'id' => $request['order_id']])->first();

        // try {
        //     $fcm_token = $order->customer->cm_firebase_token;
        //     if ($request['status'] == 'out_for_delivery') {
        //         $value = Helpers::order_status_update_message('ord_start');
        //     } elseif ($request['status'] == 'delivered') {
        //         $value = Helpers::order_status_update_message('delivered');
        //     }

        //     if ($value) {
        //         $data = [
        //             'title' => translate('order'),
        //             'description' => $value,
        //             'order_id' => $order['id'],
        //             'image' => '',
        //         ];
        //         Helpers::send_push_notif_to_device($fcm_token, $data);
        //     }
        // } catch (\Exception $e) {
        // }

        OrderManager::stock_update_on_order_status_change($order, $request['status']);

        if ($request['status'] == 'delivered' && $order['seller_id'] != null) {
            OrderManager::wallet_manage_on_order_status_change($order, 'delivery man');
            OrderDetail::where('order_id', $order->id)->update(
                ['delivery_status' => 'delivered']
            );
        }

        if ($order->order_status == 'delivered') {
            return response()->json(['success' => 0, 'message' => 'order is already delivered.'], 200);
        }

        return response()->json(['message' => 'Status updated'], 200);
    }



    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        //$d_man = $request['delivery_man'];
        // $order = Order::with(['details'])->where(['delivery_man_id' => $d_man['id'], 'id' => $request['order_id']])->first();
        // $details = $order->details;
        // foreach ($details as $det) {
        //     $det['variation'] = json_decode($det['variation']);
        //     $det['product_details'] = Helpers::product_data_formatting(json_decode($det['product_details'], true));
        // }

        $details = OrderDetail::where(['order_id' => $request['order_id']])->get();
        $bagDetails = BagsOrdersDetails::where(['order_id' => $request['order_id']])->get();
        foreach ($bagDetails as $bagDetail) {
            $bagDetail['order_id'] = (int)$bagDetail['order_id'];
            $bagDetail['bag_id'] = (int)$bagDetail['bag_id'];
        }

        $details->map(function ($query) {
            $query['variation'] = json_decode($query['variation'], true);
            $query['product_details'] = Helpers::product_data_formatting(json_decode($query['product_details'], true));
            return $query;
        });

        $bagDetails->map(function ($query) {
            $query['bag_details'] = json_decode($query['bag_details'], true);
            return $query;
        });


        return response()->json(['products' => $details, 'bags' => $bagDetails], 200);
        // return response()->json($details, 200);
    }


    public function get_all_orders(Request $request)
    {

        // $d_man = $request['delivery_man'];
        // $orders = Order::with(['shippingAddress', 'customer'])->where(['delivery_man_id' => $d_man['id']])->get();
        $d_man = $request['delivery_man'];
        if (isset($request['from']) && isset($request['to'])) {
            $orders = Order::where(['delivery_man_id' => $d_man['id']])
                //->where('scheduling', '=', 1)
                ->whereBetween('updated_at', [$request['from'], $request['to']])
                ->get()
                ->makeHidden(
                    [
                        'transaction_ref', 'discount_amount', 'discount_type',
                        'coupon_code', 'shipping_method_id', 'shipping_cost', 'seller_id',
                        'seller_is', 'delivery_man_id', 'billing_address', 'billing_address_data',
                        'extra_discount', 'extra_discount_type', 'shipping_type', 'delivery_service_name', 'Detection_number', 'shipping_address_data',
                        'delivery_type', 'status_export', 'scheduling', 'third_party_delivery_tracking_id', 'pharmacy_id', 'orderBy_id', 'customer_type'
                    ]
                );
        } else {
            $orders = Order::where(['delivery_man_id' => $d_man['id']])
                //->where('scheduling', '=', 1)
                ->get()
                ->makeHidden(
                    [
                        'transaction_ref', 'discount_amount', 'discount_type',
                        'coupon_code', 'shipping_method_id', 'shipping_cost', 'seller_id',
                        'seller_is', 'delivery_man_id', 'billing_address', 'billing_address_data',
                        'extra_discount', 'extra_discount_type', 'shipping_type', 'delivery_service_name', 'Detection_number', 'shipping_address_data',
                        'delivery_type', 'status_export', 'scheduling', 'third_party_delivery_tracking_id', 'pharmacy_id', 'orderBy_id', 'customer_type'
                    ]
                );
        }

        foreach ($orders as $order) {
            if ($order->customer_type == "pharmacist") {
                $pharmacy = Pharmacy::where('user_id', '=', $order->customer_id)->get()->first();
                $user = User::where('id', '=', $pharmacy->user_id)->get()->first();
                $order['pharmacy_name'] = $pharmacy->name;
                $order['lat'] = $pharmacy->lat;
                $order['lan'] = $pharmacy->lan;
                $order['phone'] = $user->phone;
                $order['address']=$pharmacy->city."/".$pharmacy->region."/".$pharmacy->Address;
            } else {
                $pharmacy = Pharmacy::where('id', '=', $order->orderBy_id)->get()->first();
                $user = User::where('id', '=', $pharmacy->user_id)->get()->first();
                $order['pharmacy_name'] = $pharmacy->name;
                $order['lat'] = $pharmacy->lat;
                $order['lan'] = $pharmacy->lan;
                $order['phone'] = $user->phone;
                $order['address']=$pharmacy->city."/".$pharmacy->region."/".$pharmacy->Address;
            }
        }

        return response()->json($orders, 200);
    }


    public function get_last_location(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $last_data = DeliveryHistory::where(['order_id' => $request['order_id']])->latest()->first();
        return response()->json($last_data, 200);
    }




   public function order_payment_status_update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'payment_status' => 'in:paid,unpaid'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $d_man = $request['delivery_man'];
        if (Order::where(['delivery_man_id' => $d_man['id'], 'id' => $request['order_id']])->first()) {
            Order::where(['delivery_man_id' => $d_man['id'], 'id' => $request['order_id']])->update([
                'payment_status' => $request['payment_status']
            ]);
            return response()->json(['message' => translate('Payment status updated')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => translate('not found!')]
            ]
        ], 404);
    }



    public function update_fcm_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $d_man = $request['delivery_man'];
        DeliveryMan::where(['id' => $d_man['id']])->update([
            'fcm_token' => $request['fcm_token']
        ]);

        return response()->json(['message' => 'successfully updated!'], 200);
    }
}
