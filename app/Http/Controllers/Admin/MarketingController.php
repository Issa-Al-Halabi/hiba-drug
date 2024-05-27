<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\Marketing;
use App\Model\Product;
use Illuminate\Http\Request;
use Brian2694\Toastr\Facades\Toastr;
use Exception;

class MarketingController extends Controller
{
    public function list()
    {
        $lists = Marketing::join("products", "products.id", "=", "marketing.item_id")
            ->get();
        $marketing = Marketing::get("item_id");
        $products = Product::whereNotIn('products.id', $marketing)->get();
        return view('admin-views.marketing.list', compact('lists', 'products'));
    }

    public function store(Request $request)
    {

        try {
            $marketing = new Marketing();
            $marketing->item_id = $request->product_id;
            $marketing->item_type = "product";
            $marketing->save();
            Toastr::success('Added successfully!');
            return back();
        } catch (Exception $e) {
            Toastr::success('Added Failure!');
            return back();
        }
    }


    public function delete(Request $request)
    {
        try {
            $marketing = Marketing::where('item_id', '=', $request->id);
            $marketing->delete();
            return response()->json();
        } catch (Exception $e) {
            return response()->json();
        }
    }
}
