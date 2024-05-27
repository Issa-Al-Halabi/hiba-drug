<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CartManager;
use App\CPU\Helpers;
use App\CPU\OrderManager;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Model\RefundRequest;
use App\CPU\ImageManager;
use App\Model\OrderNotification;
use Illuminate\Support\Facades\DB;
use App\User;

class OrderController extends Controller
{
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        return response()->json(OrderManager::track_order($request['order_id']), 200);
    }

    public function order_cancel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $order = Order::where(['id' => $request->order_id])->first();

        if ($order['payment_method'] == 'cash_on_delivery' && $order['order_status'] == 'pending') {
            OrderManager::stock_update_on_order_status_change($order, 'canceled');
            OrderManager::stock_update_on_bag_order_status_change($order, 'canceled');
            Order::where(['id' => $request->order_id])->update([
                'order_status' => 'canceled'
            ]);

            return response()->json(translate('order_canceled_successfully'), 200);
        }

        return response()->json(translate('status_not_changable_now'), 302);
    }

    public function place_order(Request $request)
    {
      
        $validator = Validator::make($request->all(), [
            'delivery_date' => 'required|date',
        ]);
      
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->user()->user_type == "salesman") {
            $validator = Validator::make($request->all(), [
                'pharmacy_id' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            if ($request->pharmacy_id == 0) {
                return response()->json(['errors' => 'الرجاء إختيار صيدلية'], 403);
            }
        }

        DB::beginTransaction();
        try {
            //send notification
            $unique_id = $request->user()->id . '-' . rand(000001, 999999) . '-' . time();
          
            $group_id = CartManager::get_cart_group_ids($request);
            $data = [
                'payment_method' => 'cash_on_delivery',
                'order_status' => 'pending',
                'payment_status' => 'unpaid',
                'transaction_ref' => '',
                'order_group_id' => $unique_id,
                'cart_group_id' => $group_id->cart_group_id,
                'request' => $request,
            ];
			
            if ($request->user()->user_type == "salesman")
                $order_id = OrderManager::generate_order($data, $request->pharmacy_id);
            else
                $order_id = OrderManager::generate_order($data, 0);
            $order = Order::find($order_id);
            $order->billing_address = ($request['billing_address_id'] != null) ? $request['billing_address_id'] : $order['billing_address'];
            $order->billing_address_data = ($request['billing_address_id'] != null) ?  ShippingAddress::find($request['billing_address_id']) : $order['billing_address_data'];
            $order->order_note = ($request['order_note'] != null) ? $request['order_note'] : $order['order_note'];
            if ($request->user()->user_type == "salesman") {
                $order->orderBy_id = $request->pharmacy_id;
                $order->pharmacy_name = OrderManager::getPharmacyName($request->user()->user_type, $request->pharmacy_id);
            } else {
                $order->orderBy_id = 0;
                $order->pharmacy_name = OrderManager::getPharmacyName($request->user()->user_type, $request->user()->id);
            }
            $order->delivery_date = $request->delivery_date;
            $order->save();

            CartManager::cart_clean($request);
            DB::commit();
            return response()->json(translate('order_placed_successfully'), 200);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(translate('order_placed_faild'), 200);
        }
    }

    public function refund_request(Request $request)
    {

        $order_details = OrderDetail::find($request->order_details_id);

        if ($order_details->delivery_status == 'delivered') {
            $order = Order::find($order_details->order_id);
            $total_product_price = 0;
            $refund_amount = 0;
            $data = [];
            foreach ($order->details as $key => $or_d) {
                $total_product_price += ($or_d->qty * $or_d->price) + $or_d->tax - $or_d->discount;
            }

            $subtotal = ($order_details->price * $order_details->qty) - $order_details->discount + $order_details->tax;

            $coupon_discount = ($order->discount_amount * $subtotal) / $total_product_price;

            $refund_amount = $subtotal - $coupon_discount;

            $data['product_price'] = $order_details->price;
            $data['quntity'] = $order_details->qty;
            $data['product_total_discount'] = $order_details->discount;
            $data['product_total_tax'] = $order_details->tax;
            $data['subtotal'] = $subtotal;
            $data['coupon_discount'] = $coupon_discount;
            $data['refund_amount'] = $refund_amount;

            $refund_day_limit = Helpers::get_business_settings('refund_day_limit');
            $order_details_date = $order_details->created_at;
            $current = \Carbon\Carbon::now();
            $length = $order_details_date->diffInDays($current);
            $expired = false;
            $already_requested = false;
            if ($order_details->refund_request != 0) {
                $already_requested = true;
            }
            if ($length > $refund_day_limit) {
                $expired = true;
            }
            return response()->json(['already_requested' => $already_requested, 'expired' => $expired, 'refund' => $data], 200);
        } else {
            return response()->json(translate('You_can_request_for_refund_after_order_delivered'), 200);
        }
    }

    public function store_refund(Request $request)
    {

        $order_details = OrderDetail::find($request->order_details_id);

        if ($order_details->refund_request == 0) {

            $validator = Validator::make($request->all(), [
                'order_details_id' => 'required',
                'amount' => 'required',
                'refund_reason' => 'required'

            ]);
            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $refund_request = new RefundRequest;
            $refund_request->order_details_id = $request->order_details_id;
            $refund_request->customer_id = $request->user()->id;
            $refund_request->status = 'pending';
            $refund_request->amount = $request->amount;
            $refund_request->product_id = $order_details->product_id;
            $refund_request->order_id = $order_details->order_id;
            $refund_request->refund_reason = $request->refund_reason;

            if ($request->file('images')) {
                foreach ($request->file('images') as $img) {
                    $product_images[] = ImageManager::upload('refund/', 'png', $img);
                }
                $refund_request->images = json_encode($product_images);
            }
            $refund_request->save();

            $order_details->refund_request = 1;
            $order_details->save();

            return response()->json(translate('refunded_request_updated_successfully!!'), 200);
        } else {
            return response()->json(translate('already_applied_for_refund_request!!'), 302);
        }
    }


    public function refund_details(Request $request)
    {
        $order_details = OrderDetail::find($request->id);
        $refund = RefundRequest::where('customer_id', $request->user()->id)
            ->where('order_details_id', $order_details->id)->get();
        $refund = $refund->map(function ($query) {
            $query['images'] = json_decode($query['images']);
            return $query;
        });
        $order = Order::find($order_details->order_id);
        $total_product_price = 0;
        $refund_amount = 0;
        $data = [];
        foreach ($order->details as $key => $or_d) {
            $total_product_price += ($or_d->qty * $or_d->price) + $or_d->tax - $or_d->discount;
        }
        $subtotal = ($order_details->price * $order_details->qty) - $order_details->discount + $order_details->tax;
        $coupon_discount = ($order->discount_amount * $subtotal) / $total_product_price;
        $refund_amount = $subtotal - $coupon_discount;
        $data['product_price'] = $order_details->price;
        $data['quntity'] = $order_details->qty;
        $data['product_total_discount'] = $order_details->discount;
        $data['product_total_tax'] = $order_details->tax;
        $data['subtotal'] = $subtotal;
        $data['coupon_discount'] = $coupon_discount;
        $data['refund_amount'] = $refund_amount;
        $data['refund_request'] = $refund;
        return response()->json($data, 200);
    }

	//new
    public function get_notification(Request $request)
    {
        try {
            // Retrieve notifications for the authenticated user and eager load the user (pharmacy) data
            $notifications = OrderNotification::where('user_id', $request->user()->id)
                ->orderBy('created_at', 'DESC')
                ->get();

            // Retrieve the pharmacy object for the authenticated user
            $pharmacy = User::find($request->user()->id);

            // Decode the JSON data in each notification and add the pharmacy name
            $notifications->transform(function ($notification) use ($pharmacy) {
                $notification->data = json_decode($notification->data);
                $notification->pharmacy_name = $pharmacy->name ?? 'Unknown Pharmacy'; // Add pharmacy name
                return $notification;
            });

            // Return the notifications with the pharmacy names included
            return response()->json($notifications, 200);
        } catch (\Exception $e) {
            // Return an error response in case of any exception
            return response()->json(['error' => 'Something went wrong, please try again later.'], 500);
        }
    }
  
  	//new



}
