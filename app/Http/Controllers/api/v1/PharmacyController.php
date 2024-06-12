<?php

namespace App\Http\Controllers\api\v1;
use App\Http\Controllers\Controller;
use App\Pharmacy;
use App\CPU\Helpers;
use App\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{

    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $pharmacies = Pharmacy::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $pharmacies = new Pharmacy();
        }
        $pharmacies = $pharmacies->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.pharmacy.list', compact('pharmacies','search'));
    }


    public function index()
    {
        //
        $pharmacies = Pharmacy::all();
        return view('admin-views.pharmacy.list')->with('pharmacies',$pharmacies);
    }



    public function store(Request $request)
    {
        $pharmacy = new Pharmacy();
        try{
            $pharmacy->name=$request->name;
            $pharmacy->lat=$request->lat;
            $pharmacy->lan=$request->lan;
            $pharmacy->city = $request->city;
            $pharmacy->region = $request->region;

            $pharmacy->user_id = $request->user_id;
            $user_type = User::where('id',$pharmacy->user_id)->pluck('user_type')->first();

            $pharmacy->user_type_id=$user_type;

            $pharmacy->save();

            return response()->json('Pharmacy Created',200);
        }
        catch(Exception $ex){
            return response()->json('Pharmacy Not Created',200);
        }
    }



    public function show(Pharmacy $pharmacy)
    {
        $pharmacy = Pharmacy::find($pharmacy->id);
    }


    public function edit(Request $request)
    {
        try{
            $pharmacy = Pharmacy::find($request->pharmacy_id);

            $pharmacy->name=$request->name;
            $pharmacy->lat=$request->lat;
            $pharmacy->lan=$request->lan;
            $pharmacy->city = $request->city;
            $pharmacy->region = $request->region;
            $pharmacy->user_id = $request->user_id;

            $user_type = User::where('id',$pharmacy->user_id)->pluck('user_type')->first();

            $pharmacy->user_type_id=$user_type;

            $pharmacy->update();
            return response()->json('Pharmacy Updated',200);
        }
        catch(Exception $ex){
            return response()->json('Pharmacy Not Updated',200);
        }


    }


    public function update(Request $request, Pharmacy $pharmacy)
    {
        //
    }



    public function destroy(Pharmacy $pharmacy)
    {
        //
    }




    public function pharmacy_points(Request $request)
    {

      // $user = Helpers::get_customer($request);
      $user = Helpers::get_customer();
 


        try {
        $points = DB::table('pharmacies_points')->where('pharmacy_id',$user->id)->sum('points');

        $points = (int)$points;
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        return response()->json($points, 200);

    }


}
