<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Store;          //Store
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Model\Translation;


class StoresController extends Controller
{

    public function add_new()
    {
        $br = Store::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.store.add-new', compact('br'));
    }

    public function store(Request $request)
    {
        $store = new Store;
        $store->store_name = $request->store_name[array_search('en', $request->lang)];
        $store->store_image = ImageManager::upload('store/', 'png', $request->file('store_image'));
        $store->store_status = 1;
        $store->save();

        foreach($request->lang as $index=>$key)
        {
            if($request->store_name[$index] && $key != 'en')
            {
                Translation::updateOrInsert(
                    ['translationable_type'  => 'App\Model\Store',
                        'translationable_id'    => $store->id,
                        'locale'                => $key,
                        'key'                   => 'store_name'],
                    ['value'                 => $request->store_name[$index]]
                );
            }
        }
        Toastr::success('store added successfully!');
        return back();
    }

    function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search'))
        {
            $key = explode(' ', $request['search']);
            $br = Store::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('store_name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }else{
            $br = new Store();
        }
        $br = $br->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.store.list', compact('br','search'));
    }

    public function edit($id)
    {
        $b = Store::where(['id' => $id])->withoutGlobalScopes()->first();
        return view('admin-views.store.edit', compact('b'));
    }

    public function update(Request $request, $id)
    {

        $store = Store::find($id);
        $store->store_name = $request->store_name[array_search('en', $request->lang)];
        if ($request->has('store_image')) {
            $store->store_image = ImageManager::update('store/', $store['store_image'], 'png', $request->file('store_image'));
         }
        $store->save();
        foreach ($request->lang as $index => $key) {
            if ($request->store_name[$index] && $key != 'en') {
                Translation::updateOrInsert(
                    ['translationable_type' => 'App\Model\Store',
                        'translationable_id' => $store->id,
                        'locale' => $key,
                        'key' => 'store_name'],
                    ['value' => $request->store_name[$index]]
                );
            }
        }

        Toastr::success('store updated successfully!');
        return back();
    }

    public function delete(Request $request)
    {
        $translation = Translation::where('translationable_type','App\Model\Store')
                                    ->where('translationable_id',$request->id);
        $translation->delete();
        $store = Store::find($request->id);
        ImageManager::delete('brand/' . $store['store_image']);
        $store->delete();
        return response()->json();
    }

}
