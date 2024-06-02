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
        try {
            $data = [];
            $data["products"] = RewardItem::products()->get();
            $data["bags"] = RewardItem::bags()->get();
        } catch (\Exception $e) {
            return response()->json(['errors' => $e], 403);
        }
        return response()->json($data, 200);
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
            $userPointsSum = DB::table('pharmacies_points')->where('pharmacy_id', $user->id)->sum('points');
            // $userPointsSum = DB::table('pharmacies_points')->where('pharmacy_id', 6967)->sum('points');
            $userPointsSum = (int)$userPointsSum;
            $itemPoints = $rewardItem->cost * $request->quantity;

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
                'pure_price' => 0,
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
