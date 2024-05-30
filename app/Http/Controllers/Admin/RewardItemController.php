<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\RewardItem;
use Illuminate\Support\Facades\Validator;
use Brian2694\Toastr\Facades\Toastr;

use function App\CPU\translate;

class RewardItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'bag_id' => 'nullable',
            'cost' => 'required|numeric|gt:-1',
        ], [
            'product_id.required' => 'product_id is required!',
            'cost.required' => 'cost is required!',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        RewardItem::create([
            "product_id" => $request->product_id,
            'bag_id' => null,
            "cost" => $request->cost,
        ]);

        Toastr::success(translate('Product added successfully!'));
        return RewardItem::all();
        return redirect()->route('admin.product.list');
    }

    public function storeBag(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'nullable',
            'bag_id' => 'required',
            'cost' => 'required|numeric|gt:-1',
        ], [
            'bag_id.required' => 'bag_id is required!',
            'cost.required' => 'cost is required!',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        RewardItem::create([
            "product_id" => null,
            "bag_id" => $request->bag_id,
            "cost" => $request->cost,
        ]);

        Toastr::success(translate('Product added successfully!'));
        return RewardItem::all();
        return redirect()->route('admin.product.list');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function showProduct()
    {
        $rewardItem = RewardItem::products()->get();
        if (!$rewardItem) {
            return response()->json(['error' => "not found"]);
        }

        return $rewardItem;
        return view('admin.product.list', compact('rewardItem'));
    }

    public function showBag()
    {
        $rewardItem = RewardItem::bags()->get();
        if (!$rewardItem) {
            return response()->json(['error' => "not found"]);
        }

        return $rewardItem;
        return view('admin.product.list', compact('rewardItem'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'bag_id' => 'nullable',
            'cost' => 'required|numeric|gt:-1',
        ], [
            'product_id.required' => 'product_id is required!',
            'cost.required' => 'cost is required!',
        ]);

        $rewardItem = RewardItem::where("id", $id)->first();
        if (!$rewardItem) {
            return response()->json(['error' => "not found"]);
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $rewardItem->product_id = $request->product_id;
        $rewardItem->bag_id = null;
        $rewardItem->cost = $request->cost;
        $rewardItem->save();

        Toastr::success(translate('Product added successfully!'));
        return RewardItem::all();
        return redirect()->route('admin.product.list');
    }

    public function updateBag(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'nullable',
            'bag_id' => 'required',
            'cost' => 'required|numeric|gt:-1',
        ], [
            'bag_id.required' => 'bag_id is required!',
            'cost.required' => 'cost is required!',
        ]);

        $rewardItem = RewardItem::where("id", $id)->first();
        if (!$rewardItem) {
            return response()->json(['error' => "not found"]);
        }

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $rewardItem->product_id = null;
        $rewardItem->bag_id = $request->bag_id;
        $rewardItem->cost = $request->cost;
        $rewardItem->save();

        Toastr::success(translate('Product added successfully!'));
        return RewardItem::all();
        return redirect()->route('admin.product.list');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rewardItem = RewardItem::where("id", $id)->first();
        if (!$rewardItem) {
            return response()->json(['error' => "not found"]);
        }
        $rewardItem->delete();
        Toastr::success(translate('Product added successfully!'));
        return RewardItem::all();
        return redirect()->route('admin.product.list');
    }
}
