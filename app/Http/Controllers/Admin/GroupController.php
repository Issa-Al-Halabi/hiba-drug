<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Model\Group;
use Illuminate\Http\Request;
use App\CPU\Helpers;
use App\CPU\SalerManager;
use Brian2694\Toastr\Facades\Toastr;
use Exception;

class GroupController extends Controller
{
    public function city_groups_list(Request $request, $id)
    {
        $city_groups=Group::where('city_id',$id)->get();
        $city_id=$id;
        return view('admin-views.group.list', compact('city_groups','city_id'));
    }


    public function group_store(Request $request, $city_id)
    {
        try {
            $group = new Group();
            $group->city_id = $city_id;
            $group->group_name = $request->group_name;
            $group->group_num = $request->group_num;
            $group->group_status = 1;
            $group->save();
            Toastr::success('Group area added successfully!');
            return back();
        } catch (Exception $e) {
            Toastr::success('Group area added Failure!');
        }
    }


    public function edit($id)
    {
        $group = Group::where('id', '=', $id)->get()->first();
        return response()->json([
            'data' => $group
        ]);
    }

    public function update(Request $request)
    {
        try {
            $request->validate([
                'group_id'=>'required',
                'group_name'=>'required',
            ]);
            $group = Group::find($request->group_id);
            $group->group_name =  $request->get('group_name');
            $group->save();
            Toastr::success('Name updated successfully!');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Faild updated!');
            return back();
        }
    }

    public function group_delete(Request $request)
    {
        $group = Group::findOrFail($request->id);
        SalerManager::remove_users_details_group($request->id);
        $group->areas()->delete();
        $group->delete();
        Toastr::success('Group deleted successfully!');
        return response()->json();
    }

}
