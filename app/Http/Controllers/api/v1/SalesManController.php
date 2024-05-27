<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Pharmacy;
use App\Model\WorkPlan;
use App\Model\WorkPlanTask;
use App\User;
use Illuminate\Support\Facades\DB;
use function App\CPU\translate;
use App\Model\PlanDetails;
use Illuminate\Support\Facades\Validator;


class SalesManController extends Controller
{
    public function get_pharmacies(Request $request)
    {

        if($request->has("pharmacy_id"))
        {
            $pharmacies = Pharmacy::join("users", "users.id", "=", "pharmacies.user_id")
            ->where("pharmacies.id", $request->pharmacy_id)
            ->get([
                'pharmacies.id as pharmacy_id','pharmacies.name','pharmacies.lat','pharmacies.lan','pharmacies.city','pharmacies.region as area','pharmacies.user_type_id as user_type'
                ,'pharmacies.from','pharmacies.to','pharmacies.Address','pharmacies.land_number','users.phone as phone'
            ]);
        }
        else
        {
            $id=$request->user()->id;
            $pharma_ids = DB::select('select pharmacy_id from sales_pharmacy where sales_id = ?', [$id]);
            $arr = array();
            foreach ($pharma_ids as $idx) {
                array_push($arr, $idx->pharmacy_id);
            }

            $pharmacies = Pharmacy::join("users", "users.id", "=", "pharmacies.user_id")
                ->whereIn("pharmacies.id", $arr)
                ->get([
                    'pharmacies.id as pharmacy_id','pharmacies.name','pharmacies.lat','pharmacies.lan','pharmacies.city','pharmacies.region as area','pharmacies.user_type_id as user_type'
                    ,'pharmacies.from','pharmacies.to','pharmacies.Address','pharmacies.land_number','users.phone as phone'
                ]);
        }
        return response()->json($pharmacies, 200);
    }

    public function get_work_plans(Request $request)
    {
        $id=$request->user()->id;
        $plans= WorkPlan::where(['saler_id' => $id])
        ->where([['status_plan','=',1],['end_plan','>=',now()]])->get();

        foreach($plans as $plan)
        {
            $point_date=$plan->begin_plan;
            if(!isset($request['task_date']))
            {
                $pharmaciesSelectedArray = WorkPlanTask::where([['task_plan_id','=',$plan->id]
                ,['task_date','=',$plan->begin_plan]])
                ->get(['pharmacy_id']);

            }else
            {
                $pharmaciesSelectedArray = WorkPlanTask::where([['task_plan_id','=',$plan->id]
                ,['task_date','=',$request['task_date']]])
                ->get(['pharmacy_id']);
                $point_date=$request['task_date'];
            }
            $pharmacies = Pharmacy::whereIn('id', $pharmaciesSelectedArray)->get([
                'id','name','lat','lan','city','region','user_type_id as user_type','vip','from','to',
                'Address','land_number'
            ]);

            $planPharmacies = PlanDetails::where('work_plan_id', '=', $plan->id)
                ->whereIn('Wpharmacy_id', $pharmacies->pluck('id'))
                ->get()
                ->groupBy('Wpharmacy_id');

            foreach ($pharmacies as $pharmacy) {
                $planPharmacy = $planPharmacies->get($pharmacy->id);
                $pharmacy['visited'] = $planPharmacy ? 1 : 0;
            }
          
           
            $plan['pharmacies'] = $pharmacies;
            $plan['point_date']=$point_date;
        }
       return response()->json($plans, 200);
    }


    public function register_visiter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_plan_id' => 'required',
            'pharmacy_id' => 'required',
            'visited' => 'required|boolean',
            'note' => 'required|string',
            'lat' => 'required',
            'lng' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>403 ,'errors' => Helpers::error_processor($validator)], 403);
        }

        try {
            $plan = PlanDetails::where('work_plan_id', '=', $request->work_plan_id)
                ->where('Wpharmacy_id', '=', $request->pharmacy_id)->get()->first();

                if(isset($plan))
                {
                    $plan->visited = $request->visited;
                    $plan->Wnote = $request->note;
                    $plan->Wlat = $request->lat;
                    $plan->Wlng = $request->lng;
                    $plan->visit_time = now();  // $current_date_time = date('Y-m-d H:i:s');
                    $plan->update();
                }
                else{
                   $plan =new PlanDetails;
                   $plan->work_plan_id = $request->work_plan_id;
                   $plan->Wpharmacy_id = $request->pharmacy_id;
                   $plan->visited = $request->visited;
                   $plan->Wnote = $request->note;
                   $plan->Wlat = $request->lat;
                   $plan->Wlng = $request->lng;
                   $plan->visit_time = now();
                   $plan->save();
                }

            return response()->json(['status'=>200 ,'message' => translate('The visit has been registered successfully')], 200);
        } catch (\Exception $e) {
            return response()->json(['status'=>403 ,'message' => translate('error')], 403);
        }
    }



    public function get_plan_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'work_plan_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status'=>403 ,'errors' => Helpers::error_processor($validator)], 403);
        }
        try {
            $plan = PlanDetails::where('work_plan_id', '=', $request->work_plan_id)->get();
            return response()->json($plan, 200);
        } catch (\Exception $e) {
            return response()->json(['status'=>403 ,'message' => translate('error')], 403);
        }
    }





}
