<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\OrdersPoints;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Model\Translation;
use App\Model\ProductPoint;
use App\CPU\Helpers;
use App\Model\Bag;
use App\Model\PharmaciesPoints;
use App\Model\Product;

class ProductPointController extends Controller
{

    public function index(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $productpoint = ProductPoint::where('type', 'product')->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $productpoint = new ProductPoint();
        }
        $productpoint = ProductPoint::where('type', 'product')->latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.points.list', compact('productpoint', 'search'));
    }

    public function create()
    {
        //
        $idx = array();
        $indecies = [];
        $productpoint = ProductPoint::wheretype('product')->latest()->paginate(Helpers::pagination_limit());
        foreach ($productpoint as $p) {
            array_push($idx, json_decode($p->type_id));
        }
        if (count($idx) > 0) {
            foreach ($idx as $id) {
                foreach ($id as $i) {
                    $indecies[] = $i;
                }
            }
        }
        $products = Product::whereNotIN('id', $indecies)->get();
        return view('admin-views.points.create', compact('productpoint', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'quantity' => 'required',
            'points' => 'required',
        ], [
            'quantity.required' => 'Quantity is required!',
            'points.required' => 'Points amount is required!',
        ]);

        $productpoint = new ProductPoint();
        $productpoint->type = 'product';
        $productpoint->type_id =  json_encode($request->products);
        $productpoint->quantity = $request->quantity;
        $productpoint->points = $request->points;
        $productpoint->save();
      
        Toastr::success('Points added successfully!');
        return back();
    }


    public function edit($id, Request $request)
    {
        //
        $productpoint = ProductPoint::whereId($id)->first();

        $idx = json_decode($productpoint->type_id);
        $old_products = Product::whereIn('id', $idx)->get();
        // $products = Product::all();
        $idx = array();
        $indecies = [];
        $productpoint1 = ProductPoint::wheretype('product')->latest()->paginate(Helpers::pagination_limit());
        foreach ($productpoint1 as $p) {
            array_push($idx, json_decode($p->type_id));
        }
        foreach ($idx as $id) {
            foreach ($id as $i) {
                $indecies[] = $i;
            }
        }
        $products = Product::whereNotIN('id', $indecies)->get();
        return view('admin-views.points.edit', compact('productpoint', 'old_products', 'products'));
    }


    public function update($id, Request $request)
    {
        //

        try {
            $productpoint = ProductPoint::findOrFail($id);
            $idx = json_encode($request->products);

            // $productpoint = ProductPoint::whereId();
            $productpoint->type = 'product';
            // $productpoint->type = $request->type;
            $productpoint->type_id = $idx;
            $productpoint->quantity = $request->quantity;
            $productpoint->points = $request->points;
            $productpoint->save();
            Toastr::success('Point updated successfully!');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::success('Point update Fail!');
            return redirect()->back();
        }
    }


    public function destroy(Request $request)
    {
        //

        $translation = Translation::where('translationable_type', 'App\Model\ProductPoint')
            ->where('translationable_id', $request->id);
        $translation->delete();
        $productpoint = ProductPoint::find($request->id);
        $productpoint->delete();
        return response()->json();
    }
    public function bag_point_index(Request $request)
    {
        //
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $productpoint = ProductPoint::where('type', 'bag')->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $productpoint = new ProductPoint();
        }
        $productpoint = ProductPoint::where('type', 'bag')->latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.points.bag_point_list', compact('productpoint', 'search'));
    }


    public function bag_point_create()
    {
        //
        $productpoint = ProductPoint::where('type', 'bag')->latest()->paginate(Helpers::pagination_limit());
        // $products = Bag::all();
        $idx = array();
        $indecies = [];
        $productpoint1 = ProductPoint::wheretype('bag')->latest()->paginate(Helpers::pagination_limit());
        foreach ($productpoint1 as $p) {
            array_push($idx, json_decode($p->type_id));
        }
        foreach ($idx as $id) {
            foreach ($id as $i) {
                $indecies[] = $i;
            }
        }
        $products = Bag::whereNotIN('id', $indecies)->get();
        return view('admin-views.points.bag_point_create', compact('productpoint', 'products'));
    }

    public function bag_point_store(Request $request)
    {
        $request->validate([
            // 'type' => 'required',
            // 'type_id' => 'required',
            // 'quantity' => 'required',
            'points' => 'required',
        ], [
            // 'type.required' => ' Type is required!',
            // 'type_id.required' => 'Product/Bag is required!',
            // 'quantity.required' => 'Quantity is required!',
            'points.required' => 'Points amount is required!',
        ]);
        $idx = json_encode($request->products);

        $productpoint = new ProductPoint();
        $productpoint->type = 'bag';
        // $productpoint->type = $request->type;
        $productpoint->type_id = $idx;
        $productpoint->quantity = $request->quantity;
        $productpoint->points = $request->points;
        $productpoint->save();
        Toastr::success('Points added successfully!');
        return back();
    }


    public function bag_point_edit($id, Request $request)
    {
        $productpoint = ProductPoint::whereId($id)->first();
      dd($productpoint);

        $idx = json_decode($productpoint->type_id);
      
        $old_products = Bag::whereIn('id', $idx)->get();
        $idx = array();
        $indecies = [];
        $productpoint1 = ProductPoint::wheretype('bag')->latest()->paginate(Helpers::pagination_limit());
        foreach ($productpoint1 as $p) {
            array_push($idx, json_decode($p->type_id));
        }
        foreach ($idx as $id) {
            foreach ($id as $i) {
                $indecies[] = $i;
            }
        }
        $products = Bag::whereNotIN('id', $indecies)->get();
        return view('admin-views.points.bag_point_edit', compact('productpoint', 'old_products', 'products'));
    }

    public function bag_point_update($id, Request $request)
    {
        try {
            $productpoint = ProductPoint::findOrFail($id);
            $idx = json_encode($request->products);
            $productpoint->type = 'bag';
            $productpoint->type_id = $idx;
            $productpoint->quantity = $request->quantity;
            $productpoint->points = $request->points;
            $productpoint->save();
            Toastr::success('Point updated successfully!');
            return redirect()->back();
        } catch (\Throwable $th) {
            Toastr::success('Point update Fail!');
            return redirect()->back();
        }
    }

    public function bag_point_destroy(Request $request)
    {
        $translation = Translation::where('translationable_type', 'App\Model\ProductPoint')
            ->where('translationable_id', $request->id);
        $translation->delete();
        $productpoint = ProductPoint::find($request->id);
        $productpoint->delete();
        return response()->json();
    }

    public function order_points(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $productpoint = OrdersPoints::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('price', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $productpoint = new OrdersPoints();
        }
        $productpoint = OrdersPoints::latest()->paginate(Helpers::pagination_limit());
        return view('admin-views.points.order_points.list', compact('productpoint', 'search'));
    }

    public function order_points_store(Request $request)
    {
        $request->validate([
            // 'type' => 'required',
            // 'type_id' => 'required',
            // 'quantity' => 'required',
            'points' => 'required',
        ], [
            // 'type.required' => ' Type is required!',
            // 'type_id.required' => 'Product/Bag is required!',
            // 'quantity.required' => 'Quantity is required!',
            'points.required' => 'Points amount is required!',
        ]);

        $productpoint = new OrdersPoints();
        $productpoint->price = $request->price;
        $productpoint->points = $request->points;
        $productpoint->save();
        Toastr::success('Order Points added successfully!');
        return back();
    }

    public function order_points_destroy(Request $request)
    {
        $translation = Translation::where('translationable_type', 'App\Model\OrdersPoints')
            ->where('translationable_id', $request->id);
        $translation->delete();
        $productpoint = OrdersPoints::find($request->id);
        $productpoint->delete();
        return response()->json();
    }


    public function pharmacies_points(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);

            $pharmacies = PharmaciesPoints::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->Where('name', 'like', "%{$value}%");
                }
            });

            $query_param = ['search' => $request['search']];
        } else {
            $pharmacies = new PharmaciesPoints();
        }
        $pharmacies = PharmaciesPoints::groupBy('id_of_pharmacy')->with('pharmacy')->selectRaw('sum(points) as sum, pharmacy_id,id')->latest()->paginate(Helpers::pagination_limit());

        return view('admin-views.points.pharmacies_points', compact('pharmacies', 'search'));
    }
}
