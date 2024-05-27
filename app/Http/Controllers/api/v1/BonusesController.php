<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Model\Bonus;
use Illuminate\Http\Request;

class BonusesController extends Controller
{

    public function index()
    {
        try{
            $bonuses = Bonus::all();
        }
        catch (\Exception $e) {

        }
        return response()->json($bonuses, 200);
    }


    public function unlock_products(Request $request)
    {
        try{
            $bonuses = Bonus::where('master_product_id',$request->main_product_id)->get();
            foreach($bonuses as $b){
                $b->status= 1;
                $b->save();
            }
        }
        catch (\Exception $e) {
        }
        return response()->json($bonuses, 200);
    }
    public function lock(Request $request)
    {

        try{
            $bonuses = Bonus::where('master_product_id',$request->main_product_id)->get();
            foreach($bonuses as $b){
                $b->status= 0;
                $b->save();
            }
        }
        catch (\Exception $e) {
        }
        return response()->json($bonuses, 200);
    }

}
