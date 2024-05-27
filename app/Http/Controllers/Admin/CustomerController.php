<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Group;
use App\Model\Area;
use App\Model\City;
use App\Pharmacy;
use App\User;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function customer_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $customers = User::with(['orders'])
                ->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('f_name', 'like', "%{$value}%")
                            ->orWhere('l_name', 'like', "%{$value}%")
                            ->orWhere('phone', 'like', "%{$value}%")
                            ->orWhere('email', 'like', "%{$value}%");
                    }
                });
            $query_param = ['search' => $request['search']];
        } else {
            $customers = User::with(['orders']);
        }
        $customers = $customers->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.customer.list', compact('customers', 'search'));
    }

    public function status_update(Request $request)
    {
        User::where(['id' => $request['id']])->update([
            'is_active' => $request['status']
        ]);

        DB::table('oauth_access_tokens')
            ->where('user_id', $request['id'])
            ->delete();

        return response()->json([], 200);
    }

    public function view(Request $request, $id)
    {

        $customer = User::find($id);
        if (isset($customer)) {
            $query_param = [];
            $search = $request['search'];
            $orders = Order::where(['customer_id' => $id]);
            if ($request->has('search')) {

                $orders = $orders->where('id', 'like', "%{$search}%");
                $query_param = ['search' => $request['search']];
            }
            $orders = $orders->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
            return view('admin-views.customer.customer-view', compact('customer', 'orders', 'search'));
        }
        Toastr::error('Customer not found!');
        return back();
    }

    public function edit($id)
    {

        $user = User::with(['pharmacy'])->where(['id' => $id])->withoutGlobalScopes()->get()->first();
        $cus_area = Area::where('id', $user->area_id)->get()->first();
        $cus_group = Group::where('id', $cus_area->group_id)->get()->first();
        $cus_city = City::where('id', $cus_group->city_id)->get()->first();

        return view('admin-views.pharmacy.edit', compact('user', 'cus_area', 'cus_group', 'cus_city'));
    }


    public function groups(Request $request, $cityId)
    {
        if (isset($request->cityId)) {
            $city_id = $request->cityId;
        } else {
            $city_id = $cityId;
        }

        $groups = Group::where('city_id', $city_id)->get();
        return response()->json([
            'groups' => $groups
        ]);
    }

    public function areas(Request $request, $groupId)
    {
        if (isset($request->groupId)) {
            $group_id = $request->groupId;
        } else {
            $group_id = $groupId;
        }

        $areas = Area::where('group_id', $group_id)->get();
        return response()->json([
            'areas' => $areas
        ]);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'street_address' => 'required|string',
            'f_name' => 'required|string',
            'l_name' => 'required|string',
            'lat' => 'between:-90,90',
            'lng' => 'between:-90,90',
            'to' => 'required',
            'from' => 'required',
            'to_pm' => 'required',
            'from_pm' => 'required',
            'land_number' => 'required|numeric',
            'city_id' => 'required|numeric',
            'email' => 'required',
            'area_id' => 'required|numeric',
            'group_id' => 'required|numeric',
            'phone' => 'required|unique:users,phone,' . $id,
            'num_id' => 'required|unique:users,pharmacy_id,' . $id,
            //'card_number' => 'required|unique:pharmacies,card_number,'.$pharmacy->id,
        ]);

        if ($validator->fails()) {
            Toastr::success($validator->errors());
            return back();
        }
        $emailNew = "Hiba_Store" . $request->num_id . "@hiba.sy";
        $group = Group::where('id', $request->group_id)->get()->first();
        $area = Area::where('id', $request->area_id)->get()->first();
        $city = City::where('id', $request->city_id)->get()->first();
        DB::beginTransaction();
        try {
            $user = User::where('id', $id)->get()->first();
            $pharmacy = Pharmacy::where('user_id', $id)->get()->first();
          	// new
            $orders = Order::where('orderBy_id', $pharmacy->id)->get();
            foreach ($orders as $order) {
                $order->pharmacy_name = $request->name;
                $order->save();
            }
            // new
          	$user->name = $request->name;
            $user->f_name = $request->f_name;
            $user->l_name = $request->l_name;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->street_address = $request->street_address;
            $user->country = $group->group_name;
            $user->city = $city->city_name;
            $user->zip = 30303;
            $user->pharmacy_id = $request->num_id;
            $user->area_id = $request->area_id;
            if ($request->has('password'))
                $user->password = bcrypt($request->password);

            $pharmacy->lat = $request->lat;
            $pharmacy->lan = $request->lan;
            $pharmacy->city = $city->city_name;
            $pharmacy->region = $area->area_name;
            $pharmacy->name = $request->name;
            $pharmacy->Address = $request->street_address;
            $pharmacy->from = $request->from;
            $pharmacy->to = $request->to;
            $pharmacy->from_pm = $request->from_pm;
            $pharmacy->to_pm = $request->to_pm;
            $pharmacy->land_number = $request->land_number;
            $pharmacy->card_number = 0;
            $user->save();
            $pharmacy->save();
            DB::commit();
            Toastr::success('pharmacy updated successfully!');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('pharmacy updated faild!');
            return back();
        }
    }

    public function delete($id)
    {
        $customer = User::find($id);
        $p = Pharmacy::where('user_id', $customer->id);
        $p->delete();
        $customer->delete();
        Toastr::success('Customer deleted successfully!');
        return back();
    }
}
