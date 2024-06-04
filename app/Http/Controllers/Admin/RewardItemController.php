<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\RewardItem;
use App\Model\Product;
use App\Model\Bag;
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
    public function addProduct()
    {
      
        $idx = array();
        $productpoint = [];
        $productpoint = RewardItem::where('bag_id')->latest()->paginate(Helpers::pagination_limit());
		$indecies=[];
        foreach ($productpoint as $p) {
            array_push($idx, json_decode($p->product_id));
        };
        if (count($idx) > 0) {
            foreach ($idx as $id) {
                    $indecies[] = $id;
            }
        }
        $products = Product::whereNotIN('id', $indecies)->get();
        return view('admin-views.points_store.create',compact('productpoint', 'products'));
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

        Toastr::success(translate('Added successfully!'));
        // return RewardItem::all();
        return redirect()->route('admin.reward-item.showProduct');
    }

    public function addBag()
    {
        $idx = array();
        $productpoint = [];
        $productpoint = RewardItem::where('product_id')->latest()->paginate(Helpers::pagination_limit());
		$indecies=[];
        foreach ($productpoint as $p) {
            array_push($idx, json_decode($p->bag_id));
        };
        if (count($idx) > 0) {
            foreach ($idx as $id) {
                    $indecies[] = $id;
            }
        }
        
        $products = Bag::whereNotIN('id', $indecies)->get();
        return view('admin-views.points_store.createbag',compact('productpoint', 'products'));
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

        Toastr::success(translate('Added successfully!'));
        // return RewardItem::all();
        return redirect()->route('admin.reward-item.showBag');
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
  
  
  public function showProduct(Request $request)
    {

        $query_param = [];
        $search = $request['search'];

        $rewardItemall = RewardItem::products()->get();


        if ($request->has('search')) {
            $key = explode(' ', $search); // Ensure $search is passed correctly as a string
            
            $rewardItemall = $rewardItemall->filter(function ($saler) use ($key) {
                foreach ($key as $value) {
                    if (stripos($saler->product->name, $value) !== false) {
                        return true;
                    }
                }
                return false;
            });
            
            $query_param = ['search' => $search];
        }

        $rewardItem  = [];
        foreach ($rewardItemall as $saler) {
            $rewardItem [] = [
                'id' => $saler->id,
                'name' => $saler->product->name,
                'bag_id' => $saler->bag_id,
                'cost' => $saler->cost,
            ];
        }

        return view('admin-views.points_store.list', compact('rewardItem','search'));
    }

/*
    public function showProduct(Request $request)
    {

        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            
            $RewardItem = Product::where(function ($q) use ($key) {
                
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $RewardItem = new RewardItem();
        }
        
        $rewardItemall = RewardItem::products()->get();


        $rewardItem  = [];
        foreach ($rewardItemall as $saler) {
            $product = Product::where('id', '=', $saler->product_id)->first();
            if ($product) {
                $rewardItem [] = [
                    'id' => $saler->id,
                    'name' => $product->name,
                    'bag_id' => $saler->bag_id,
                    'cost' => $saler->cost,
                ];
            }
        }

        return view('admin-views.points_store.list', compact('rewardItem','search'));
    }

    public function showBag(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            
            $RewardItem = Bag::where(function ($q) use ($key) {
                
                foreach ($key as $value) {
                    $q->orWhere('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $RewardItem = new RewardItem();
        }
        
        $rewardItemall = RewardItem::Bags()->get();
        
        $rewardItem  = [];
        foreach ($rewardItemall as $saler) {
            
            $Bag = Bag::where('id', '=', $saler->bag_id)->first();
            if ($Bag) {
                $rewardItem [] = [
                    'id' => $saler->id,
                    'name' => $Bag->bag_name,
                    'cost' => $saler->cost,
                ];
            }
        }
        return view('admin-views.points_store.listbag', compact('rewardItem','search'));
    }
*/
      public function showBag(Request $request)
      {
          $query_param = [];
          $search = $request->input('search'); // Use input() to fetch the search parameter

          $rewardItemall = RewardItem::Bags()->get();

          if ($request->has('search')) {
              $key = explode(' ', $search); // Ensure $search is passed correctly as a string

              $rewardItemall = $rewardItemall->filter(function ($saler) use ($key) {
                  foreach ($key as $value) {
                      if (stripos($saler->bag->bag_name, $value) !== false) {
                          return true;
                      }
                  }
                  return false;
              });

              $query_param = ['search' => $search];
          }

          $rewardItem = [];
          foreach ($rewardItemall as $saler) {
              $rewardItem[] = [
                  'id' => $saler->id,
                  'name' => $saler->bag->bag_name,
                  'cost' => $saler->cost,
              ];
          }

          return view('admin-views.points_store.listbag', compact('rewardItem', 'search'));
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

    public function showUpdateProduct($id)
    {

        $rewardItemall = RewardItem::where("id", $id)->first();
        if (!$rewardItemall) {
            return response()->json(['error' => "not found"]);
        }
        $rewardItem  = [];
        $product = Product::where('id', '=', $rewardItemall->product_id)->first();
        $rewardItemall['name'] = $product->name;
        return view('admin-views.points_store.edit', compact('rewardItem','rewardItemall'));
        // return view('admin-views.points_store.listbage', compact('rewardItem'));
    }

    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
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

        Toastr::success(translate('Update successfully!'));
        // return RewardItem::all();
        // return redirect()->route('admin-views.points_store.edit');
        return redirect()->route('admin.reward-item.showProduct');
    }

    public function showUpdateBag($id)
    {
        $rewardItemall = RewardItem::where("id", $id)->first();
        if (!$rewardItemall) {
            return response()->json(['error' => "not found"]);
        }
        $rewardItem  = [];

        $Bag = Bag::where('id', '=', $rewardItemall->bag_id)->first();
        $rewardItemall['name'] = $Bag->bag_name;
        return view('admin-views.points_store.editbage', compact('rewardItem','rewardItemall'));
        // return view('admin-views.points_store.listbage', compact('rewardItem'));
    }

    public function updateBag(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
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

        Toastr::success(translate('Update successfully!'));
        return redirect()->route('admin.reward-item.showBag');

    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyProduct($id)
    {
        $rewardItem = RewardItem::where("id", $id)->first();
        if (!$rewardItem) {
            return response()->json(['error' => "not found"]);
        }
        $rewardItem->delete();
        Toastr::success(translate('Destroy successfully!'));
        return redirect()->route('admin.reward-item.showProduct');
    }
    public function destroyBag($id)
    {
        $rewardItem = RewardItem::where("id", $id)->first();
        if (!$rewardItem) {
            return response()->json(['error' => "not found"]);
        }
        $rewardItem->delete();
        Toastr::success(translate('Destroy successfully!'));
        return redirect()->route('admin.reward-item.showBag');
    }
}
