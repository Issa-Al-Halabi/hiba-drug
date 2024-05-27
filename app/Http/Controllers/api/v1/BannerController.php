<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    public function get_banners(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request['banner_type'] == 'all') {
            $banners = Banner::where(['published' => 1])->with(['product'])->get();
        } elseif ($request['banner_type'] == 'main_banner') {
            $bannersBrand = Banner::where(['published' => 1,'resource_type'=>'brand', 'banner_type' => 'Main Banner'])->with(['brand'])->get();
            $bannersProduct = Banner::where(['published' => 1,'resource_type'=>'product', 'banner_type' => 'Main Banner'])->with(['product'])->get();
        } else {
            $banners = Banner::where(['published' => 1, 'banner_type' => 'Footer Banner'])->with(['product'])->get();
        }
        $bannersProduct =$bannersProduct->map(function($data){

            if($data['resource_type']=='product'){
                $data['product']=Helpers::product_data_formatting($data['product']);
            }

            return $data;
        });

        $result = $bannersProduct->merge($bannersBrand);
        return response()->json($result, 200);
    }
}
