<?php

namespace App\Http\Controllers\Admin;

use App\User;
use function App\CPU\translate;
use App\Http\Controllers\Controller;
use App\Pharmacy;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\DB;
use App\Model\City;
use App\Model\SalerTeam;
use App\Model\SalerReview;
use App\Model\Admin;
use App\Model\Area;
use App\Model\Group;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\CPU\Helpers;
use App\CPU\SalerManager;
use Exception;

class SalesManController extends Controller
{

    public function index()
    {
        return view('admin-views.sales-man.index');
    }


    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $sales_men = User::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $sales_men = new User();
        }

        $sales_men = $sales_men->latest()->where(['user_type' => 'salesman'])->paginate(25)->appends($query_param);
        foreach ($sales_men as $saler) {
            $team = SalerTeam::where('saler_id', '=', $saler->id)->get()->first();
          	if(isset($team->team)) {
            	$saler['team'] = $team->team;
            } else {
            	$saler['team'] = null;
            }
        }
        return view('admin-views.sales-man.list', compact('sales_men', 'search'));
    }


    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $sales_men = User::where(['user_type' => 'salesman'])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.sales-man.partials._table', compact('sales_men'))->render()
        ]);
    }



    public function preview($id)
    {

        $all_areas_assign = [];
        $all_groups_assign = [];
        $sm = User::where(['id' => $id])->first();
        $pharma_id  = DB::select('select pharmacy_id from sales_pharmacy where sales_id = ?', [$id]);
        $area_id  = DB::select('select area_id from sales_area where sales_id = ?', [$id]);
        $group_id  = DB::select('select group_id from sales_group where sales_id = ?', [$id]);

        $arr = array();
        foreach ($pharma_id as $idx) {
            array_push($arr, $idx->pharmacy_id);
        }

        $arrArea = array();
        foreach ($area_id as $idx) {
            array_push($arrArea, $idx->area_id);
        }

        $arrGroup = array();
        foreach ($group_id as $idg) {
            array_push($arrGroup, $idg->group_id);
        }

        $pharmacies = Pharmacy::whereIn('id', $arr)->get();
        $areas = Area::whereIn('id', $arrArea)->get();
        $groups = Group::whereIn('id', $arrGroup)->get();

        if (count($arr) > 0) {
            $all_pharmacies = Pharmacy::whereNotIn('id', $arr)->get();
        } else {
            $all_pharmacies = Pharmacy::all();
        }

        if (count($arrArea) > 0) {
            $all_areas = Area::whereNotIn('id', $arrArea)->get();

            $all_areas_assign = Area::join("group_area", "group_area.id", "=", "areas.group_id")
                ->whereIn("areas.id", $arrArea)
                ->get([
                    'group_area.group_name as group_name', 'areas.id as area_id',
                    'areas.area_name as area_name',
                ]);
        } else {
            $all_areas = Area::all();
        }

        if (count($arrGroup) > 0) {
            $all_groups = Group::whereNotIn('id', $arrGroup)->get();

            $all_groups_assign = Group::join("cities", "cities.id", "=", "group_area.city_id")
                ->whereIn("group_area.id", $arrGroup)
                ->get([
                    'cities.city_name as city_name', 'group_area.id as group_id',
                    'group_area.group_name as group_name',
                ]);
        } else {
            $all_groups = Group::all();
        }

        return view('admin-views.sales-man.view', compact('sm', 'pharmacies', 'all_groups', 'all_groups_assign', 'all_pharmacies', 'all_areas', 'all_areas_assign'));
    }

    //Pharmecy
    public function unassign(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'saler_id' => 'required',
        ], [
            'saler_id.required' => 'something wrong!',
        ]);
        $decryptedSalerId = Crypt::decrypt($request->saler_id);
        DB::delete('delete FROM sales_pharmacy WHERE pharmacy_id =' . $id . ' AND sales_id=' . $decryptedSalerId . '');
        SalerManager::remove_pharmacy_plan($id, $decryptedSalerId);
        return redirect()->back();
    }


    public function assign(Request $request, $id)
    {
        $pharmacies = $request->assigned_pharmacies;
        foreach ($pharmacies as $pharma) {
            $query = DB::table('sales_pharmacy')
                ->select(['id'])
                ->where('sales_id', '=', $id)
                ->where('pharmacy_id', '=', $pharma)
                ->get()->first();
            if (isset($query) == false) {
                DB::insert('insert into sales_pharmacy (sales_id, pharmacy_id) values (?, ?)', [$id, $pharma]);
            }
        }
        return redirect()->back();
    }

    //Area
    public function unassign_area(Request $request, $id)
    {
        $area_id = $id;
        $validator = Validator::make($request->all(), [
            'saler_id' => 'required',
        ], [
            'saler_id.required' => 'something wrong!',
        ]);
        $decryptedSalerId = Crypt::decrypt($request->saler_id);

        $pharmacies = Pharmacy::join("users", "users.id", "=", "pharmacies.user_id")
            ->where("users.area_id", $area_id)
            ->get([
                'pharmacies.id as pharma_id'
            ]);

        foreach ($pharmacies as $pharma) {
            DB::delete('delete FROM sales_pharmacy WHERE pharmacy_id =' . $pharma['pharma_id'] . ' AND sales_id=' . $decryptedSalerId . '');
            SalerManager::remove_pharmacy_plan($pharma['pharma_id'], $decryptedSalerId);
        }

        DB::delete('delete FROM sales_area WHERE area_id =' . $area_id . ' AND sales_id=' . $decryptedSalerId . '');
        return redirect()->back();
    }

    public function assign_area(Request $request, $id)
    {
        $areas_id = $request->assigned_areas;
        foreach ($areas_id as $area_id) {
            DB::insert('insert into sales_area (sales_id, area_id) values (?, ?)', [$id, $area_id]);
            $users = User::with(['pharmacy'])->where('area_id', $area_id)->where('user_type', 'pharmacist')->get();
            foreach ($users as $user) {
                $pharmacy = Pharmacy::where('user_id', $user->id)->get()->first();
                $query = DB::table('sales_pharmacy')
                    ->select(['id'])
                    ->where('sales_id', '=', $id)
                    ->where('pharmacy_id', '=', $pharmacy->id)
                    ->get()->first();
                if (isset($query) == false) {
                    DB::insert('insert into sales_pharmacy (sales_id, pharmacy_id) values (?, ?)', [$id, $pharmacy->id]);
                }
            }
        }
        return redirect()->back();
    }


    public function unassign_group(Request $request, $id)
    {
        $group_id = $id;
        $array_area = [];
        $validator = Validator::make($request->all(), [
            'saler_id' => 'required',
        ], [
            'saler_id.required' => 'something wrong!',
        ]);
        $decryptedSalerId = Crypt::decrypt($request->saler_id);

        $area_ids = Area::where('group_id', '=', $group_id)->get(['id']);
        foreach ($area_ids as $a) {
            array_push($array_area, $a->id);
        }
        $pharmacies = Pharmacy::join("users", "users.id", "=", "pharmacies.user_id")
            ->whereIn("users.area_id", $array_area)
            ->get([
                'pharmacies.id as pharma_id'
            ]);

        foreach ($pharmacies as $pharma) {
            DB::delete('delete FROM sales_pharmacy WHERE pharmacy_id =' . $pharma['pharma_id'] . ' AND sales_id=' . $decryptedSalerId . '');
            SalerManager::remove_pharmacy_plan($pharma['pharma_id'], $decryptedSalerId);
        }
        foreach ($area_ids as $a) {
            DB::delete('delete FROM sales_area WHERE area_id =' . $a->id . ' AND sales_id=' . $decryptedSalerId . '');
        }
        DB::delete('delete FROM sales_group WHERE group_id =' . $group_id . ' AND sales_id=' . $decryptedSalerId . '');
        return redirect()->back();
    }


    public function assign_group(Request $request, $id)
    {
        $groups_id = $request->assigned_groups;
        foreach ($groups_id as $group_id) {
            DB::insert('insert into sales_group (sales_id, group_id) values (?, ?)', [$id, $group_id]);
            $areas = Area::where('group_id', '=', $group_id)->get();
            foreach ($areas as $area) {
                $query = DB::table('sales_area')
                    ->select(['id'])
                    ->where('sales_id', '=', $id)
                    ->where('area_id', '=', $area->id)
                    ->get()->first();
                if (isset($query) == false) {
                    DB::insert('insert into sales_area (sales_id, area_id) values (?, ?)', [$id, $area->id]);
                }
                $users = User::with(['pharmacy'])->where('area_id', $area->id)->where('user_type', 'pharmacist')->get();
                foreach ($users as $user) {
                    $pharmacy = Pharmacy::where('user_id', $user->id)->get()->first();
                    $query = DB::table('sales_pharmacy')
                        ->select(['id'])
                        ->where('sales_id', '=', $id)
                        ->where('pharmacy_id', '=', $pharmacy->id)
                        ->get()->first();
                    if (isset($query) == false) {
                        DB::insert('insert into sales_pharmacy (sales_id, pharmacy_id) values (?, ?)', [$id, $pharmacy->id]);
                    }
                }
            }
        }
        return redirect()->back();
    }


    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'f_name' => 'required',
                'phone' => 'required',
                'email' => 'required|unique:users,email',
                'phone' => 'required|unique:users,phone',
                'area_id' => 'required',
                'group_id' => 'required',
                'city_id' => 'required',
                'team_char' => 'required',
                'account_number' => 'required|unique:users,account_number',
            ], [
                'email.required' => 'Email is required!',
                'phone.required' => 'Phone is required!',
                'f_name.required' => 'First name is required!',
                'team_char.required' => 'Team is required!',
                'account_number.required' => 'Account number is required!',
            ]);

            $area = Area::where('id', $request->area_id)->get()->first();
            $group = Group::where('id', $area->group_id)->get()->first();
            $city = City::where('id', $group->city_id)->get()->first();

            $sm = new User();
            $sm->user_type = 'salesman';
            $sm->name = $request->f_name . ' ' . $request->l_name;
            $sm->f_name = $request->f_name;
            $sm->l_name = $request->l_name;
            $sm->email = $request->email;
            $sm->area_id = $request->area_id;
            $sm->city = $city->city_name;
            $sm->country = $group->group_name;
            $sm->phone = $request->phone;
            $sm->password = bcrypt($request->password);
            $sm->account_number = $request->account_number;
            $sm->save();

            $team = new SalerTeam();
            $team->saler_id = $sm->id;
            $team->team = $request->team_char;
            $team->save();

            DB::commit();
            Toastr::success('Sales-man added successfully!');
            return redirect('admin/sales-man/list');
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::error('Sales-man added faild!');
            return back();
        }
    }

    public function edit($id)
    {
        $sales_man = User::find($id);
        return view('admin-views.sales-man.edit', compact('sales_man'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'required|unique:users,phone,' . $id,
            'phone' => 'required|unique:users,phone,' . $id,
            'account_number' => 'required|unique:users,account_number,' . $id,
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        $sales_man = User::where(['id' => $id, 'user_type' => 'salesman'])->first();
        if (isset($sales_man) && $request['email'] != $sales_man['email']) {
            $request->validate([
                'email' => 'required|unique:users',
            ]);
        }
        $sales_man->user_type = 'salesman';
        $sales_man->f_name = $request->f_name;
        $sales_man->l_name = $request->l_name;
        $sales_man->email = $request->email;
        $sales_man->phone = $request->phone;
        $sales_man->account_number = $request->account_number;
        if (isset($request->password)) {
            $sales_man->password = strlen($request->password) > 1 ? bcrypt($request->password) : $sales_man['password'];
        }
        $sales_man->save();
        Toastr::success('Saler updated successfully!');
        return redirect('admin/sales-man/list');
    }

    public function destroy(Request $request)
    {
        SalerManager::remove_salesman_details($request->id);
        $sales_man = User::find($request->id);
        $sales_man->delete();
        Toastr::success(translate('Sales-man removed!'));
        return back();
    }

    public function areas(Request $request, $catId)
    {
        if (isset($request->city_id)) {
            $city_id = $request->city_id;
        } else {
            $city_id = $catId;
        }

        $areas = Area::where('city_id', $city_id)->get();
        return response()->json([
            'areas' => $areas
        ]);
    }

    public function set_date(Request $request)
    {

        $from = $request['from'];
        $to = $request['to'];
        $team_char = $request['team_char'];

        session()->put('from_date', $from);
        session()->put('to_date', $to);
        session()->put('team', $team_char);

        $previousUrl = strtok(url()->previous(), '?');
        return redirect()->to($previousUrl . '?' . http_build_query(['from_date' => $request['from'], 'to_date' => $request['to'], 'team' => $request['team_char']]))->with(['from' => $from, 'to' => $to]);
    }


    public function orders_report_team(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        if (session()->has('team') == false) {
            session()->put('team', 'A');
        }

        return view('admin-views.sales-man.order-index');
    }

    public function review($id)
    {
        $saler = User::with(['saler_reviews'])->where(['id' => $id])->first();
        $reviews = SalerReview::where('saler_id', '=', $id)->paginate(6);

        return view('admin-views.sales-man.review', compact('saler', 'reviews'));
    }

    public function reviews(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $lists = User::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $lists = User::where('user_type', '=', 'salesman');
        }
        $lists->Where('user_type', '=', "salesman");
        $lists = $lists->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.sales-man.reviewList', compact('lists', 'search'));
    }

    public function store_review(Request $request)
    {
        $request->validate([
            'rating' => 'required',
            'comment' => 'required',
            'saler_id' => 'required',
        ], [
            'rating.required' => 'Rating is required!',
            'comment.required' => 'Comment is required!',
            'saler_id.required' => 'Saler ID is required!',
        ]);

        try {
            $emp = Admin::where('id', '=', auth('admin')->id())->get()->first();
            $salerReview = new SalerReview;
            $salerReview->saler_id = $request->saler_id;
            $salerReview->saler_comment = $request->comment;
            $salerReview->saler_rating = $request->rating;
            $salerReview->emp_name = $emp->name;
            $salerReview->save();
            Toastr::success('Review added successfully!');
            return back();
        } catch (Exception $e) {
            return back();
            Toastr::success('Review added Failure!');
        }
    }

    public function pharmacies_assigned()
    {
        $pharmaciesAssigned = DB::table('sales_pharmacy')->get(['pharmacy_id']);
        $pharmaciesAssignedId = [];
        foreach ($pharmaciesAssigned as $row) {
            $pharmaciesAssignedId[] = (array) $row;
        }
        $pharmaciesNotAssigned = Pharmacy::whereNotIn('id', $pharmaciesAssignedId)->get();
        return view('admin-views.sales-man.pharmaciesList', compact('pharmaciesNotAssigned'));
    }
}
