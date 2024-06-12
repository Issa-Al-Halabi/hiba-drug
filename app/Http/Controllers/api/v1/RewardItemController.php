<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Model\RewardItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\CPU\Helpers;
use App\Model\PharmaciesPoints;
use Illuminate\Support\Facades\DB;

class RewardItemController extends Controller
{

     public function getAll()
    {
        $user = auth()->user();

        try {
            $data = [];

            $data["products"] = RewardItem::products()->with('product')->get()->map(function ($model) {
                $model["product"]["reward_id"] = $model->id;
                $model["product"]["cost"] = $model->cost;
                return $model["product"];
            });

            $data["bags"] = RewardItem::bags()->with('bag')->get()->map(function ($model) {
                $model["bag"]["reward_id"] = $model->id;
                $model["bag"]["cost"] = $model->cost;
              
             	$bag_order_details = $model["bag"]["bag_order_details"];
              	unset($model["bag"]["bag_order_details"]);
              
              	$model["bag"]["bag_order_details"] = json_decode($bag_order_details[0]["bag_details"], true);

                return $model["bag"];
            });

            $points = DB::table('pharmacies_points')->where('pharmacy_id', $user->id)->sum('points');
          $points = (int)$points;

            $data["points_of_pharmacy"] = $points;


            return response()->json($data, 200);
        } catch (\Exception $e) {

            return response()->json(['errors' => $e->getMessage()], 403);
        }
    }

   
    public function buy(Request $request)
    {
  
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'quantity' => 'required',
        ], [
            'id.required' => 'id is required!',
            'quantity.required' => 'quantity is required!',
        ]);

        if ($validator->errors()->count() > 0) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        try {
            $rewardItem = RewardItem::where("id", $request->id)->first();

            if (!$rewardItem) {
                return response()->json(['error' => "not found"]);
            }
             $user = Helpers::get_customer($request);

         // return $user->id;
            $userPointsSum = DB::table('pharmacies_points')->where('pharmacy_id', $user->id)->sum('points');
          //   $userPointsSum = DB::table('pharmacies_points')->where('pharmacy_id', 7175)->sum('points');
            $userPointsSum = (int)$userPointsSum;
            $itemPoints = $rewardItem->cost * $request->quantity;
          
          //dd($itemPoints , $userPointsSum);

            // No Enough Points
            if ($itemPoints > $userPointsSum) {
                return response()->json(['errors' => "No Enough Points"], 403);
            }

            $apiUserController = new CartController();

            $request = new Request([
                'id' => $rewardItem->getTypeId(),
                'quantity' => $request->quantity,
                'type' => $rewardItem->getType(),
                // 'pure_price' => $rewardItem->getPrice(),
                'pure_price' => 2,
              	"reward"=>""
            ]);
          
            $add_to_cart = $apiUserController->add_to_cart($request);
        
            $add_to_cart_response = json_decode(collect($add_to_cart)->toJson(), true);
          
            // substract user Points 
            if ($add_to_cart_response["original"]['status'] == 1) {
                // $userPoints = PharmaciesPoints::where('pharmacy_id', 6967)->get();
                $userPoints = PharmaciesPoints::where('pharmacy_id',  $user->id)->get();
                $this->subPoints($userPoints, $itemPoints);
            }
        } catch (\Exception $e) {
            return response()->json(['errors' => $e->getMessage()], 403);
        }
        return response()->json($add_to_cart, 200);
    }


    public function subPoints($userPoints, $itemPoints)
    {
        foreach ($userPoints as $point) {
            if ($itemPoints > $point->points) {
                $itemPoints =  $itemPoints - $point->points;
                $point->delete();
            } else {
                $point->points =  $point->points -  $itemPoints;
                $point->save();
                break;
            }
        }
    }
}
