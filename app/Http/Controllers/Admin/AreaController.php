<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Area;
use Illuminate\Http\Request;
use App\CPU\Helpers;
use App\CPU\SalerManager;
use Brian2694\Toastr\Facades\Toastr;
use Exception;

class AreaController extends Controller
{
    public function group_areas_list(Request $request, $id)
    {
        $group_areas = Area::join("group_area", "group_area.id", "=", "areas.group_id")
            ->where("areas.group_id", $id)
            ->get([
                'areas.area_name', 'areas.id as area_id', 'areas.area_num'
            ]);
        $group_id = $id;
        return view('admin-views.area.list', compact('group_areas', 'group_id'));
    }

    public function area_store(Request $request, $group_id)
    {

        try {
            $area = new Area();
            $area->group_id = $group_id;
            $area->area_name = $request->area_name;
            $area->area_num = $request->area_num;
            $area->area_status = 1;
            $area->save();
            Toastr::success('Area added successfully!');
            return back();
        } catch (Exception $e) {
            return back();
            Toastr::success('Area added Failure!');
        }
    }

    public function edit($id)
    {
        $area = Area::where('id', '=', $id)->get()->first();
        return response()->json([
            'data' => $area
        ]);
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'area_id' => 'required',
                'area_name' => 'required',
            ]);
            $area = Area::find($request->area_id);
            $area->area_name =  $request->get('area_name');
            $area->save();
            Toastr::success('Name updated successfully!');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Faild updated!');
            return back();
        }
    }


    public function area_delete(Request $request)
    {
        $area = Area::find($request->id);
        SalerManager::remove_users_details($request->id);
        $area->delete();
        Toastr::success('Area deleted successfully!');
    }
 
     public function url(Request $request)
    {
       $url="https://dashboard.hibadrugstore.sy";
        return response()->json($url, 200);
       
    }

}
