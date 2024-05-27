<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Repository\Pharmacy\PharmacyInterface;
use App\Repository\User\UserInterface;
use Illuminate\Support\Facades\Redirect;
use Brian2694\Toastr\Facades\Toastr;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use App\Pharmacy;
use App\CPU\Helpers;
use App\User;
use App\Model\UserImportExcel;
use App\Model\Group;
use App\Model\City;
use App\Model\Area;
use Exception;
use Illuminate\Http\Request;

use Throwable;

class PharmacyController extends Controller
{
    protected $pharmacyI;
    protected $UserI;

    public function __construct(PharmacyInterface $pharmacyI, UserInterface $UserI)
    {
        $this->pharmacyI = $pharmacyI;
        $this->UserI = $UserI;
    }

    function list(Request $request, $status)
    {
        $query_param = [];
        $search = $request['search'];
        $pending = false;
        if ($request->has('search')) {

            $key = explode(' ', $request['search']);

            $pharmacies = User::with(['pharmacy'])->where('user_type', "pharmacist");
            $userIds = Pharmacy::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            })->get(['user_id']);
            $query_param = ['search' => $request['search']];
        } else {
            $pharmacies = User::with(['pharmacy'])->where('user_type', "pharmacist");
            $userIds = Pharmacy::get(['user_id']);
        }

        if ($status != 'Pending') {
            $pharmacies = $pharmacies->where(['is_active' => 1])->whereIn('id', $userIds);
        } else {
            $pending = true;
            $pharmacies = $pharmacies->where(['is_active' => 0])->whereIn('id', $userIds);
        }

        $pharmacies = $pharmacies->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.pharmacy.list', compact('pharmacies', 'search', 'pending', 'status'));
    }


    public function activation(Request $request, $id, $status)
    {
        $User = User::with(['pharmacy'])->where('id', $request->id)->get()->first();
        if (!isset($User->pharmacy_id) && $User->pharmacy_id == null) {
            Toastr::success('Please enter the missing data before activating (Account Number)');
            return back();
        }
        if (!isset($User->pharmacy->card_number) && $User->pharmacy->card_number == "") {
            Toastr::success('Please enter the missing data before activating (Card Number)');
            return back();
        }

        if ($status == 1) {
            $User->is_active = 1;
        } else {
            $User->is_active = 0;
        }
        $User->save();
        return Redirect::back();
    }


    public function vip(Request $request, $id, $status)
    {
        $pharmacy = Pharmacy::find($id);
        if ($status == 1) {
            $pharmacy->vip = 1;
        } else {
            $pharmacy->vip = 0;
        }
        $pharmacy->save();
        return Redirect::back();
    }


    public function store(Request $request)
    {
        //
        $pharmacy = new Pharmacy();
        try {
            $pharmacy->name = $request->name;
            $pharmacy->lat = $request->lat;
            $pharmacy->lan = $request->lan;
            $pharmacy->city = $request->city;
            $pharmacy->region = $request->region;
            $pharmacy->user_id = $request->user_id;
            $user_type = User::where('id', $pharmacy->user_id)->get();
            $pharmacy->user_type = $user_type->user_type;
            $pharmacy->save();
            return response()->json('Pharmacy Created', 200);
        } catch (Exception $ex) {
            return response()->json('Pharmacy Not Created', 200);
        }
    }

    public function edit(Request $request)
    {
        //
        $pharmacy = Pharmacy::find($request->pharmacy_id);
        $pharmacy->name = $request->name;
        $pharmacy->lat = $request->lat;
        $pharmacy->lan = $request->lan;
        $pharmacy->city = $request->city;
        $pharmacy->region = $request->region;
        $pharmacy->user_id = $request->user_id;
        $user_type = User::where('id', $pharmacy->user_id)->get();
        $pharmacy->user_type = $user_type->user_type;
        $pharmacy->save();
        Toastr::success('Pharmacy updated successfully.');
        return back();
    }


    public function update(Request $request, Pharmacy $pharmacy)
    {
        return view('admin-views.pharmacy.edit', compact('pharmacies'));
    }


    public function destroy(Request $request)
    {
        $pharama = Pharmacy::find($request->id);
        $pharama->delete();
    }


    public function bulk_import_index(Request $request)
    {

        $query_param = [];
        $search = $request['search'];
        $pending = false;
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $pharmacies = UserImportExcel::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('pharmacy_name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $pharmacies = UserImportExcel::whereNotIn('id', [-2]);
        }
        $pharmacies = $pharmacies->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.pharmacy.bulk-import', compact('pharmacies', 'search'));
    }


    public function bulk_import_data(Request $request)
    {

        try {
            $collections = (new FastExcel)->import($request->file("pharmacies_file"));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }
        $data = [];
        $statusUpdate = 0;
        $statusCreate = 0;

        $fields = [
            'المدينة',
            'الكتلة',
            'المنطقة',
            'وضع الزبون',
            'رقم البطاقة',
            'رمز الحساب',
            'خط العرض',
            'خط الطول',
            'الاسم',
            'هاتف 1',
            'هاتف 2',
            'خليوي',
            'العنوان'
        ];
        foreach ($collections as $collection) {

            for ($i = 0; $i < count($fields); $i++) {
                if (!isset($collection[$fields[$i]])) {
                    Toastr::error(' الحقل ' . $fields[$i] . ' غير موجود ');
                    return back();
                }
            }
            $city_id = 0;
            $group_id = 0;
            $area_id = 0;
            if ($this->checkFieldsAreValid($fields, $collection)) {
                $city_id = $this->compare_city($collection['المدينة']);
                $group_id = $this->compare_group($collection['الكتلة'], $city_id);
                $area_id = $this->compare_area($collection['المنطقة'], $group_id);
                $is_active = $this->compare_active($collection['وضع الزبون']);

                $name1 = (int)trim($collection['رمز الحساب'], " \t.");
                if ($name1 != 0) {
                    $user = UserImportExcel::where('num_id', '=', $name1)->get()->first();
                    if (isset($user)) {
                        $user->lat = $collection['خط العرض'];
                        $user->lng = $collection['خط الطول'];
                        $user->card_number =$collection['رقم البطاقة'];
                        $user->pharmacy_name = $collection['الاسم'];
                        $user->land_number = $collection['هاتف 1'];
                        $user->phone2 = $collection['هاتف 2'];
                        $user->phone1 = $collection['خليوي'];
                        $user->city_id = $city_id;
                        $user->group_id = $group_id;
                        $user->area_id = $area_id;
                        $user->street_address = $collection['العنوان'];
                        $user->is_active = $is_active;
                        $user->save();
                        $statusUpdate++;
                    } else {
                        array_push($data, [
                            'num_id' => $collection['رمز الحساب'],
                            'lat' => $collection['خط العرض'],
                            'lng' => $collection['خط الطول'],
                            'card_number' => $collection['رقم البطاقة'],
                            'pharmacy_name' => $collection['الاسم'],
                            'land_number' => $collection['هاتف 1'],
                            'phone1' => $collection['هاتف 2'],
                            'phone2' => $collection['خليوي'],
                            'group_id' => $group_id,
                            'city_id' => $city_id,
                            'area_id' => $area_id,
                            'street_address' => $collection['العنوان'],
                            'is_active' => $is_active,
                        ]);
                        $statusCreate++;
                    }
                }

            }

        }

        try {
            DB::table('user_import_excel')->insert($data);
            Toastr::success('(' . $statusCreate . ') - Pharmacise imported successfully! & (' . $statusUpdate . ')Pharmacise updated successfully!');
            return back();
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong , please try again.');
            return back();
        }
    }

    public function checkFieldsAreValid($fields, $collection)
    {
        for ($i = 0; $i < count($fields); $i++) {
            $fild = $collection[$fields[$i]];
            if (is_null($fild)) {
                return false;
            }
        }
        return true;
    }

    public function  compare_city($city)
    {
        $name1 = trim($city, " \t\n.");
        $cityDB = City::where('city_name', '=', $name1)->get()->first();
        if (isset($cityDB)) {
            return $cityDB->id;
        } else {
            $cityNew = new City();
            $cityNew->city_name = $name1;
            $cityNew->city_status = 1;
            $cityNew->save();
            return $cityNew->id;
        }
    }

    public function  compare_group($group, $cityId)
    {
        $name1 = trim($group, " \t\n.");
        $groupDB = Group::where('group_name', '=', $name1)->get()->first();
        if (isset($groupDB)) {
            return $groupDB->id;
        } else {
            $groupNew = new Group();
            $groupNew->group_name = $name1;
            $groupNew->group_status = 1;
            $groupNew->city_id = $cityId;
            $groupNew->save();
            return $groupNew->id;
        }
    }



    public function  compare_area($area, $groupId)
    {
        $name1 = trim($area, " \t\n.");
        $areaDB = Area::where('area_name', '=', $name1)->get()->first();
        if (isset($areaDB)) {
            return $areaDB->id;
        } else {
            $areaNew = new Area();
            $areaNew->area_name = $name1;
            $areaNew->area_status = 1;
            $areaNew->group_id = $groupId;
            $areaNew->save();
            return $areaNew->id;
        }
    }


    public function  compare_active($active)
    {
        // Declaration of strings
        $name1 = $active;
        $name1 = trim($name1, " \t.");
        // Use strcmp() function
        if (strcmp($name1, "مغلق") == 0 || strcmp($name1, "خارج الخدمة") == 0 || strcmp($name1, "خارج الخدمه") == 0) {
            return 0;
        } else {
            return 1;
        }
    }

    public function  generate_excel(Request $request)
    {

        $user = User::with(['pharmacy'])->where('id', $request->id)->get()->first();
        $storage = [];
        if ($user->is_active == 1)
            $cusStatus = "قيد العمل";
        else
            $cusStatus = "مغلق";

        if ($user->user_type == "pharmacist")
            $work = "صيدلية";
        else
            $work = "";


        $sales_id  = DB::select('select sales_id from sales_pharmacy where pharmacy_id = ?', [$user->pharmacy->id]);
        $arr = array();
        foreach ($sales_id as $idx) {
            array_push($arr, $idx->sales_id);
        }

        if (isset($arr[0]))
            $salesMan1 = User::where('id', $arr[0])->get()->first();

        if (isset($arr[1]))
            $salesMan2 = User::where('id', $arr[1])->get()->first();


        if (isset($salesMan1))
            $salesManName1 = $salesMan1->f_name . '' . $salesMan1->l_name;
        else
            $salesManName1 = "";

        if (isset($salesMan2))
            $salesManName2 = $salesMan2->f_name . '' . $salesMan2->l_name;
        else
            $salesManName2 = "";

        if ($user->pharmacy_id != null) {
            $account_num = $user->pharmacy_id;
        } else {
            $account_num = "";
        }

        $storage[] = [
            'الاسم' =>  $user->pharmacy->name,
            'رمز الحساب' => $account_num,
            'التصنيف' => "New",
            'هاتف 1' => $user->pharmacy->land_number,
            'هاتف 2' => "",
            'خليوي' => $user->phone,
            'ملاحظات' => "",
            'الكتلة' => $user->country,
            'المدينة' => $user->pharmacy->city,
            'المنطقة' => $user->pharmacy->region,
            'العنوان' => $user->street_address,
            'وضع الزبون' => $cusStatus,
            'العمل' => $work,
            'A مندوب فريق ' => $salesManName1,
            'B مندوب فريق ' => $salesManName2,
        ];


        $xlsx = ".xlsx";
        $result = $user->name . '-' . now() . '' . $xlsx;
        return (new FastExcel($storage))->download($result);
    }

    public function pharmacy_Import_edit(Request $request, $id)
    {
        $pharmacy = UserImportExcel::findOrFail($id);
        $cus_area = Area::where('id', $pharmacy->area_id)->get()->first();
        $cus_group = Group::where('id', $cus_area->group_id)->get()->first();
        $cus_city = City::where('id', $cus_group->city_id)->get()->first();
        $email = "Hiba_Store" . $id . "@hiba.sy";
        return view('admin-views.pharmacy-import.edit', compact('email', 'pharmacy'))->with('cus_area', $cus_area)->with('cus_group', $cus_group)->with('cus_city', $cus_city);
    }


    public function pharmacy_Import_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_name' => 'required|string',
            //'f_name' => 'string',
            //'l_name' => 'string',
            'lat' => 'required|between:-90,90',
            'lng' => 'required|between:-90,90',
            //'to' => 'date_format:H:i',
            //'from' => 'date_format:H:i',
            'city_id' => 'required|numeric',
            'area_id' => 'required|numeric',
            'group_id' => 'required|numeric',
            'password' => 'required',
            'phone1' => 'required|unique:user_import_excel,phone1,' . $id,
            //'phone2' => 'required',
            'num_id' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            Toastr::success($validator->errors());
            return back();
        }
        ($request->has('f_name') && $request->f_name != "") ?  $fname = $request->f_name : $fname = $request->pharmacy_name;
        ($request->has('l_name') && $request->l_name != "") ?  $l_name = $request->l_name : $l_name = $request->pharmacy_name;
        ($request->has('to') && $request->to != "00:00" && $request->to != "00:00:00") ?  $to = $request->to : $to = "17:00:00";
        ($request->has('from') && $request->from != "00:00" && $request->from != "00:00:00") ?  $from = $request->from : $from = "09:00:00";
        ($request->has('Land_number')) ?  $Land_number = $request->Land_number : $Land_number = 0;

        $pharmacy = UserImportExcel::find($id);
        $pharmacy->pharmacy_name = $request->pharmacy_name;
        $pharmacy->password = $request->password;
        $pharmacy->f_name = $fname;
        $pharmacy->num_id = $request->num_id;
        $pharmacy->l_name = $l_name;
        $pharmacy->lat = $request->lat;
        $pharmacy->lng = $request->lng;
        $pharmacy->phone1 = $request->phone1;
        $pharmacy->phone2 = $request->phone2;
        $pharmacy->to = $to;
        $pharmacy->card_number = 0;
        $pharmacy->from = $from;
        $pharmacy->land_number = $Land_number;
        $pharmacy->city_id = $request->city_id;
        $pharmacy->area_id = $request->area_id;
        $pharmacy->group_id = $request->group_id;
        $pharmacy->save();
        Toastr::success('Pharmacy updated successfully.');
        return back();
    }


    public function pharmacy_Import_destroy(Request $request, $id)
    {
        try {
            $pharama = UserImportExcel::findOrFail($id);
            $pharama->delete();
            Toastr::success('Pharmacy deleted successfully.');
            return back();
        } catch (Throwable $e) {
            Toastr::error($e);
            return back();
        }
    }


    public function activation_export($id)
    {
        try {
            $pharma = UserImportExcel::findOrFail($id);
            if ((isset($pharma->phone1) && $pharma->phone1 == 0) || !isset($pharma->phone1))
                $pharma->phone1 = rand(900000000, 999999999);

            if ((isset($pharma->l_name) && $pharma->l_name == "") || !isset($pharma->l_name))
                $pharma->l_name = $pharma->pharmacy_name;

            if ((isset($pharma->f_name) && $pharma->f_name == "") || !isset($pharma->f_name))
                $pharma->f_name = $pharma->pharmacy_name;

            if ((isset($pharma->num_id) && $pharma->num_id == "") || !isset($pharma->num_id))
                $pharma->num_id = rand(100000000, 999999999);

            $password = (isset($pharma->password)) ? bcrypt($pharma->password) : bcrypt(123456789);
            ($pharma->to && $pharma->to != "00:00" && $pharma->to != "00:00:00") ?  $to = $pharma->to : $to = "17:00:00";
            ($pharma->from && $pharma->from != "00:00" && $pharma->from != "00:00:00") ?  $from = $pharma->from : $from = "09:00:00";
            ($pharma->Land_number && is_numeric($pharma->Land_number)) ?  $Land_number = $pharma->Land_number : $Land_number = 0;

            $cityDB = City::where('id', '=', $pharma->city_id)->get()->first();
            $groupDB = Group::where('id', '=', $pharma->group_id)->get()->first();
            $areaDB = Area::where('id', '=', $pharma->area_id)->get()->first();
            $randomId = rand(5000, 10000000);
            $randomId2 = rand(1, 10000);

            $emailNew = "Hiba_" . $randomId . $randomId2 . "@hiba.sy";

            if ((isset($pharma->lat) && isset($pharma->lng)) && (is_numeric($pharma->lat) &&  is_numeric($pharma->lng))) {
                $LAT = $pharma->lat;
                $LNG = $pharma->lng;
            } else {
                $LAT = 33.48320296410215;
                $LNG = 36.35307297680653;
            }

            $dataUser = [
                'name' => $pharma->f_name,
                'f_name' => $pharma->f_name,
                'l_name' => $pharma->l_name,
                'phone' => $pharma->phone1,
                'pharmacy_id' => $pharma->num_id,
                'email' => $emailNew,
                'password' => $password,
                'user_type' =>  "pharmacist",
                'area_id' => $pharma->area_id,
                'street_address' => $pharma->street_address,
                'country' => $groupDB->group_name,
                'city' => $cityDB->city_name
            ];
            $dataPharmacy = [
                'name' => $pharma->pharmacy_name,
                'lat' => $LAT,
                'lan' => $LNG,
                'city' => $cityDB->city_name,
                'region' => $areaDB->area_name,
                'user_type_id' => "pharmacist",
                'from' =>  $from,
                'to' => $to,
                'card_number' => $pharma->card_number,
                'Address' => $pharma->street_address,
                'land_number' => $Land_number,
            ];

            $isFound = $this->UserI->searchAccountNumber($pharma->num_id);
            if (!is_null($isFound)) {
                //update
                $userNew = $this->UserI->storeOrUpdate($pharma->num_id, $dataUser);
                $this->pharmacyI->storeOrUpdate($userNew->id, $dataPharmacy);
            } else {
                //insert
                $userNew = $this->UserI->storeOrUpdate($id = null, $dataUser);
                $this->pharmacyI->storeOrUpdate($userNew->id, $dataPharmacy);
            }

            $pharma->delete();
            Toastr::success('Pharmacy activation successfully.');
            return back();
        } catch (Exception $e) {
            Toastr::error('Pharmacy activation faild.');
            return back();
        }
    }


    public function generate_excel_all_pharmacies()
    {

        $users = User::with(['pharmacy'])->where('user_type', '=', 'pharmacist')->get();
        $storage = [];
        foreach ($users as $user) {

            $salesMan1 = "";
            $salesMan2 = "";
            if ($user->is_active == 1)
                $cusStatus = "قيد العمل";
            else
                $cusStatus = "مغلق";

            if ($user->user_type == "pharmacist")
                $work = "صيدلية";
            else
                $work = "";


            $sales_id  = DB::select('select sales_id from sales_pharmacy where pharmacy_id = ?', [$user->pharmacy->id]);
            $arr = [];
            foreach ($sales_id as $idx) {
                array_push($arr, $idx->sales_id);
            }

            if (isset($arr[0]))
                $salesMan1 = User::where('id', $arr[0])->get()->first();

            if (isset($arr[1]))
                $salesMan2 = User::where('id', $arr[1])->get()->first();


            if (isset($salesMan1) && $salesMan1 != "")
                $salesManName1 = $salesMan1->f_name . '' . $salesMan1->l_name;
            else
                $salesManName1 = "";

            if (isset($salesMan2) && $salesMan2 != "")
                $salesManName2 = $salesMan2->f_name . '' . $salesMan2->l_name;
            else
                $salesManName2 = "";

            if ($user->pharmacy_id != null) {
                $account_num = $user->pharmacy_id;
            } else {
                $account_num = "";
            }

            $storage[] = [
                'الاسم' =>  $user->pharmacy->name,
                'رمز الحساب' => $account_num,
                'التصنيف' => "New",
                'هاتف 1' => $user->pharmacy->land_number,
                'هاتف 2' => "",
                'خليوي' => $user->phone,
                'ملاحظات' => "",
                'الكتلة' => $user->country,
                'المدينة' => $user->pharmacy->city,
                'المنطقة' => $user->pharmacy->region,
                'العنوان' => $user->street_address,
                'وضع الزبون' => $cusStatus,
                'العمل' => $work,
                'A مندوب فريق ' => $salesManName1,
                'B مندوب فريق ' => $salesManName2,
            ];
        }


        $xlsx = ".xlsx";
        $result = "Pharmacies" . '-' . now() . '' . $xlsx;
        return (new FastExcel($storage))->download($result);
    }
}
