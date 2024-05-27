<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\Model\Area;
use App\Model\City;
use App\Model\Group;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function get_cities()
    {

        try {
            $cities = City::get()->makeHidden(
                [
                    'updated_at', 'created_at', 'deleted_at'
                ]
            );
        } catch (\Exception $e) {
        }

        return response()->json($cities, 200);
    }


    public function get_areas(Request $request)
    {

        try {
            $city_areas = City::join("group_area", "group_area.city_id", "=", "cities.id")
            ->join("areas", "areas.group_id", "=", "group_area.id")
            ->where("cities.id", $request->city_id)
            ->get()->makeHidden(
                [
                    'city_status', 'city_name', 'created_at','updated_at','city_id','group_name',
                    'group_status', 'group_num', 'group_id'
                ]);

        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }

        return response()->json($city_areas, 200);
    }

}
