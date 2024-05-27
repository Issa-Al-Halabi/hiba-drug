<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Bag;
use App\Model\Area;
use App\Model\BagsSetting;
use App\Model\BagProduct;
use App\Pharmacy;
use App\Model\ProductPoint;

use Illuminate\Http\Request;

class BagController extends Controller
{
    public function get_bags(Request $request)
    {
        try {
            $details = array();
            $user = $request->user();
            $bags = Bag::active();

            if ($user->user_type == "salesman") {
                if ($request->has('pharmacy_id') && $request['pharmacy_id']!="") {
                    $pharamcyDetails=BagController::getPharmacyDetailsForBags($request->pharmacy_id);
                } else {
                    foreach ($bags as $bag) {
                        $bagSetting = BagsSetting::where('bag_id', '=', $bag->id)->get()->first();
                        if ($bagSetting->all == 1)
                            array_push($details, $bag);
                    }
                    return response()->json($details, 200);
                }
            }
            else {
                $pharamcyDetails=BagController::getPharmacyDetailsForBags($user->pharmacy->id);
            }
            foreach ($bags as $bag) {
                $bagSetting = BagsSetting::where('bag_id', '=', $bag->id)->get()->first();
                if ($bagSetting->all == 1)
                    array_push($details, $bag);
                else {
                    if ($bagSetting->vip == 1 && $pharamcyDetails['pharmacyIsVip'] == 1) {
                        array_push($details, $bag);
                    } elseif ($bagSetting->vip == 0 && $pharamcyDetails['pharmacyIsVip'] == 0 || $bagSetting->vip == 0 && $pharamcyDetails['pharmacyIsVip'] == 1 || $bagSetting->vip == 1 && $pharamcyDetails['pharmacyIsVip'] == 0) {
                        if ($bagSetting->custom == 0 && $bagSetting->vip == 0 && $pharamcyDetails['pharmacyIsVip'] == 0 && $bagSetting->custom_pharmacy == 0) {
                            array_push($details, $bag);
                        }
                        if ($bagSetting->custom == 1) {
                            $group_ids = json_decode($bagSetting->group_ids);
                            for ($i = 0; $i < count($group_ids); $i++) {
                                if ($group_ids[$i] == $pharamcyDetails['PharmacyGroupId']) {
                                    array_push($details, $bag);
                                }
                            }
                        }
                        if ($bagSetting->custom_pharmacy == 1) {
                            $pharmacy_ids = json_decode($bagSetting->pharmacy_ids);
                            for ($i = 0; $i < count($pharmacy_ids); $i++) {
                                if ($pharmacy_ids[$i] == $pharamcyDetails['pharmacyId']) {
                                    array_push($details, $bag);
                                }
                            }
                        }
                    }
                }
            }
            $points = ProductPoint::where('type', 'bag')->get();
            foreach ($details as $p) {
                foreach ($points as $point) {

                    $idx = json_decode($point->type_id);
                    foreach ($idx as $d) {

                        if ($p->id == $d) {
                            $p['points'] = $point->points;
                        } else {
                            $p['points'] = '0';
                        }
                    }
                }
            }
        } catch (\Exception $e) {
        }
        return response()->json($details, 200);
    }



    //86 rows

    public function get_bag_products(Request $request)
    {
        try {
            $bag_products = BagProduct::join("products", "products.id", "=", "products_bag.product_id")
                ->where("products_bag.bag_id", $request->bag_id)
                ->get([
                    'products.name', 'products.thumbnail',
                    'products_bag.product_count',
                    'products_bag.product_price', 'products_bag.product_total_price',
                ]);
        } catch (\Exception $e) {
        }

        return response()->json($bag_products, 200);
    }

    public function getPharmacyDetailsForBags($pharamcyId)
    {
        $pharmacy = Pharmacy::where('id', '=', $pharamcyId)->get()->first();
        $area = Area::where('id', '=', $pharmacy->customer->area_id)->get()->first();
        return $pharmacyDeatils=[
            'pharmacyId'=>$pharmacy->id,
            'PharmacyGroupId'=>$area->group_id,
            'pharmacyIsVip'=>$pharmacy->vip
        ];
    }

}
