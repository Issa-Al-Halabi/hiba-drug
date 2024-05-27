<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Model\Coupon;
use App\Model\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {

        try {
            $couponLimit = Order::where('customer_id', $request->user()->id)
                ->where('coupon_code', $request['code'])->count();

            $coupon = Coupon::where(['code' => $request['code']])
                ->where('limit', '>', $couponLimit)
                ->where('status', '=', 1)
                ->whereDate('start_date', '<=', Carbon::parse()->toDateString())
                ->whereDate('expire_date', '>=', Carbon::parse()->toDateString())->first();
            //$coupon = Coupon::where(['code' => $request['code']])->first();
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        return response()->json($coupon, 200);
    }

    public function coupons(Request $request)
    {
        # code...
        try {
        $coupons = Coupon::where('status','=',1)->get();
        $data = [];
        foreach($coupons as $coupon){
            $code = encrypt($coupon->code);
            $data['id'] = $coupon->id;
            $data['title'] = $coupon->title;
            $data['start_date'] = $coupon->start_date;
            $data['expire_date'] = $coupon->expire_date;
            $data['min_purchase'] = $coupon->min_purchase;
            $data['discount'] = $coupon->discount;
            $data['code'] = $code;
        }
    }
    catch (\Exception $e) {
        return response()->json(['errors' => $e], 403);
    }
    return response()->json($data, 200);

    }


}
