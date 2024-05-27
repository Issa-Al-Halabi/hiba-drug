<?php

namespace App\Http\Controllers\Admin;


use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\BaseController;
use App\Model\Bag;
use App\Model\Group;
use App\Model\BagsSetting;
use App\Model\BagsOrdersDetails;
use App\Model\BagProduct;
use App\Model\Product;
use App\Pharmacy;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\Translation;
use Illuminate\Support\Facades\Validator;
use Exception;

class BagController extends BaseController
{


    public function bag_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $br = bag::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('bag_name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $br = new Bag();
        }
        $br = $br->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.bag.list', compact('br', 'search'));
    }


    public function bag_add_new()
    {
        $br = Bag::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.bag.add-new', compact('br'));
    }


    public function bag_store(Request $request)
    {

        $request->validate([
            'bag_name' => 'required',
            'bag_description' => 'nullable',
            'end_date' => 'required',
            'bag_image' => 'required',
            'demand_limit' => 'required',
        ], [
            'bag_name.required' => ' Name is required!',
            'bag_description.required' => 'Description name is required!',
            'bag_image.required' => 'Image is required!',
            'end_date.required' => 'Expiry date is required!',
            'demand_limit.required' => 'Demand limit is required!',
        ]);

        $bag = new Bag;
        $bag->bag_name = $request->bag_name;
        $bag->bag_description = $request->bag_description;
        $bag->end_date = $request->end_date;
        $bag->bag_image = ImageManager::upload('bag/', 'png', $request->file('bag_image'));
        $bag->bag_status = 1;
        $bag->demand_limit = $request->demand_limit;
        $bag->save();

        $bagSetting = new BagsSetting;
        $bagSetting->all = 1;
        $bagSetting->bag_id = $bag->id;
        $bagSetting->save();
        Toastr::success('bag added successfully!');
        return back();
    }

    public function bag_edit($id)
    {
        $b = Bag::where(['id' => $id])->withoutGlobalScopes()->first();
        return view('admin-views.bag.edit', compact('b'));
    }

    public function bag_update(Request $request, $id)
    {

        $request->validate([
            'bag_name' => 'required',
            'bag_description' => 'nullable',
            'end_date' => 'required',
            'demand_limit' => 'required',
        ], [
            'bag_name.required' => ' Name is required!',
            'bag_description.required' => 'Description name is required!',
            'end_date.required' => 'Expiry date is required!',
            'demand_limit.required' => 'Demand limit is required!',
        ]);


        try {
            $bag = Bag::findOrFail($id);
            $bag->bag_name = $request->bag_name;
            if ($request->has('bag_image')) {
                $bag->bag_image = ImageManager::update('bag/', $bag['bag_image'], 'png', $request->file('bag_image'));
            }
            $bag->bag_description = $request->bag_description;
            $bag->end_date = $request->end_date;
            $bag->demand_limit = $request->demand_limit;
            $bag->save();
            Toastr::success('bag updated successfully!');
            return back();
        } catch (\Throwable $th) {
            Toastr::success('bag update Fail!');
            return back();
        }
    }

    public function bag_delete(Request $request)
    {
        $translation = Translation::where('translationable_type', 'App\Model\Bag')
            ->where('translationable_id', $request->id);
        $translation->delete();
        $bag = Bag::find($request->id);
        $bagsProduct = BagProduct::where('bag_id', '=', $request->id)->get();
        foreach ($bagsProduct as $b) {
            $bagDelete = BagProduct::find($b->id);
            $bagDelete->delete();
        }
        ImageManager::delete('brand/' . $bag['bag_image']);
        $bagSet = BagsSetting::where('bag_id', '=', $bag->id)->get()->first();
        if (isset($bagSet)) $bagSet->delete();
        $bag->delete();
        return response()->json();
    }


    public function bag_products_list(Request $request, $id)
    {
        $bag_products = BagProduct::join("products", "products.id", "=", "products_bag.product_id")
            ->where("products_bag.bag_id", $id)
            ->get([
                'products_bag.id as id',
                'products_bag.bag_id as bag_id',
                'products.id as product_id', 'products.name', 'products.thumbnail',
                'products_bag.product_count',
                'products_bag.product_price', 'products_bag.product_total_price',
            ]);
        $price = DB::table('products_bag')->where('bag_id', $id)->sum('product_total_price');
        $bag = Bag::findOrFail($id);
        $bag->total_price_offer = $price;
        $bag->save();

        $br = Product::orderBy('name')->get();
        $bag_id = $id;
        return view('admin-views.bag.bag-product-view', compact('bag_products', 'br', 'bag_id'));
    }


    public function bag_products_store(Request $request, $bag_id)
    {
        $request->validate([
            'product_count' => 'required',
            'product_id' => 'required',
        ], [
            'product_count.required' => 'Product Count is required!',
            'product_id.required' => 'Product is required!',
        ]);

        $freePrice = 0;
        $bagProduct = new BagProduct();
        $bagProduct->bag_id = $bag_id;
        $bagProduct->product_count = $request->product_count;
        $bagProduct->product_id = $request->product_id;
        if (isset($request->free)) {
            $bagProduct->is_gift = 1;
            $freePrice = 0;
        } else {
            $bagProduct->is_gift = 0;
            $freePrice = 1;
        }


        $product = DB::table('products')
            ->select('id', 'name', 'unit_price')
            ->where('id', $request->product_id)
            ->get()->first();

        $bagProduct->product_price =  $product->unit_price;
        $bagProduct->product_total_price = $product->unit_price * $request->product_count * $freePrice;
        $bagProduct->save();

        $price = DB::table('products_bag')->where('bag_id', $bag_id)->sum('product_total_price');
        $bag = Bag::findOrFail($bag_id);
        $bag->total_price_offer = $price;
        $bag->save();
        Toastr::success('Product added successfully!');
        return back();
    }

    public function bag_products_delete(Request $request)
    {
        $bag = BagProduct::findOrFail($request->id);
        $bag->delete();
        Toastr::success('Product deleted successfully!');
    }

    public function status_update(Request $request)
    {
        $bag = Bag::where(['id' => $request['id']])->get()->first();
        $success = 1;
        if ($request['status'] == 1) {
            $bag->bag_status = $request['status'];
        } else {
            $bag->bag_status = $request['status'];
        }
        $bag->save();
        return response()->json([
            'success' => $success,
        ], 200);
    }

    public function products_bag_ajax(Request $request, $id)
    {
        $subLaws = '';
        $free = 'free';
        $bagProducts = BagsOrdersDetails::where('order_id', '=', $id)->get()->first();
        $bagProducts->bag_details = json_decode($bagProducts->bag_details, true);
        for ($i = 0; $i < count($bagProducts->bag_details); $i++) {
            if ($bagProducts->bag_details[$i]['is_gift'] == 1)
                $free = 'free';
            else
                $free = '';
            $subLaws .= '<tr class="odd">
                          <td>' . $bagProducts->bag_details[$i]['product_name'] . '</td>
                          <td>' . $bagProducts->bag_details[$i]['brand_name'] . '</td>
                          <td>' . $bagProducts->bag_details[$i]['product_count'] . '</td>
                          <td>' . $bagProducts->bag_details[$i]['product_price'] . '</td>
                          <td>' . $bagProducts->bag_details[$i]['product_total_price'] . '</td>
                          <td>' . $free . '</td>' . '<tr>';
        }
        return response()->json([
            'data' => $subLaws
        ]);
    }


    public function bag_settings_store(Request $request, $id)
    {

        $request->validate([
            'all' => 'required',
        ], [
            'all.required' => 'Choose something please!',
        ]);
        try {
            $bagsSetting = BagsSetting::where('bag_id', '=', $id)->get()->first();
            $bagsSetting->all = 0;
            $bagsSetting->vip = 0;
            $bagsSetting->non_vip = 0;
            $bagsSetting->custom = 0;
            $bagsSetting->custom_pharmacy = 0;
            $bagsSetting->group_ids = "";
            $bagsSetting->pharmacy_ids = "";

            if ($request->all == 0)
                $bagsSetting->all = 1;
            elseif ($request->all == 1)
                $bagsSetting->vip = 1;
            elseif ($request->all == 2)
                $bagsSetting->non_vip = 1;
            elseif ($request->all == 3) {
                $bagsSetting->custom = 1;
                if (isset($request->group_ids))
                    $bagsSetting->group_ids = json_encode($request->group_ids);
                else
                    $bagsSetting->group_ids = "";
            } else   //Custom Pharmacy
            {
                $bagsSetting->custom_pharmacy = 1;
                if (isset($request->pharamcies_ids))
                    $bagsSetting->pharmacy_ids = json_encode($request->pharamcies_ids);
                else
                    $bagsSetting->pharmacy_ids = "";
            }

            $bagsSetting->save();
            Toastr::success('Updated successfully!');
            return back();
        } catch (Exception $e) {
            Toastr::error('Error!');
            return back();
        }
    }


    public function bag_settings(Request $request, $id)
    {
        try {
            $city_id = 0;
            $array = array();
            $array2 = array();
            $groups = [];
            $bag = BagsSetting::where('bag_id', '=', $id)->get()->first();

            if ($bag->group_ids != "") {
                $array = json_decode($bag->group_ids);
                $city = Group::where('id', '=', $array[0])->get(['city_id'])->first();
                $city_id = $city->city_id;

                $groupsSelected = Group::whereIn("id", $array)->get();
                $groups = Group::where('city_id', '=', $city_id)->get();
            }
            if ($bag->pharmacy_ids != "" && $bag->pharmacy_ids != null) {
                $array2 = json_decode($bag->pharmacy_ids);
                $pharmaciesSelected = Pharmacy::whereIn("id", $array2)->get();
            }
            $b = $id;
            $pharmacies = Pharmacy::get(['id','name']);
            return view('admin-views.bag.setting', compact('bag', 'b', 'city_id', 'array', 'groups', 'pharmacies', 'array2'));
        } catch (Exception $e) {
            Toastr::error('Error!');
            return back();
        }
    }

    public function bag_product_price(Request $request, $id)
    {
        $bagProduct = BagProduct::where('bag_id', '=', $id)
            ->where('product_id', '=', $request->product_id)->get()->first();
        return response()->json([
            'data' => $bagProduct
        ]);
    }


    public function bag_update_price(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'product_price' => 'required',
                'product_count' => 'required',
            ]);
            if ($validator->fails()) {
                Toastr::error('Faild updated!');
            }

            $bagProduct = BagProduct::where('id', '=', $request->id)->get()->first();
            $bagProduct->product_price = $request->product_price;
            $bagProduct->product_count = $request->product_count;
            if ($bagProduct->is_gift == 0)
                $bagProduct->product_total_price = ($request->product_count * $request->product_price);
            else
                $bagProduct->product_total_price = 0;
            $bagProduct->save();


            $price = DB::table('products_bag')->where('bag_id', $bagProduct->bag_id)->sum('product_total_price');
            $bag = Bag::findOrFail($bagProduct->bag_id);
            $bag->total_price_offer = $price;
            $bag->save();

            Toastr::success('updated successfully!');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Faild updated!');
            return back();
        }
    }
}
