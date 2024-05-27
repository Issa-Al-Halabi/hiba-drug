<?php

namespace App\CPU;

use App\Model\WorkPlan;
use App\Model\WorkPlanTask;
use App\Model\Area;
use App\Model\Group;
use App\User;
use App\Model\PlanDetails;
use Illuminate\Support\Facades\DB;

class SalerManager
{

    public static function getSalerPlanDetailsStatistic($planId,$fromDate,$toDate,$selerId)
    {
        $countPharmaciesInPlan = DB::table('work_plan_tasks')
        ->where('task_plan_id','=',$planId)
        ->whereBetween('task_date', [$fromDate, $toDate])
        ->count();

        $countVisited = DB::table('pharmacies_plan_details')
        ->where('work_plan_id','=',$planId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->count();

        $countNotVisited = $countPharmaciesInPlan - $countVisited;

        $totalOrdersVisited = DB::table('orders')
        ->where('customer_id', $selerId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->count();

        $totalAmountOfOrdersVisited = DB::table('orders')
        ->where('customer_id', $selerId)
        ->whereBetween('created_at', [$fromDate, $toDate])
        ->sum('order_amount');

           return [
            'countPharmaciesInPlan'=> $countPharmaciesInPlan,            //عدد الصيدليات في الخطة
            'countVisited'=> $countVisited,                              //عدد الزيارات
            'countNotVisited'=> $countNotVisited,                        //عدد الصيدليات التي لم يتم زيارتها
            'totalOrdersVisited'=> $totalOrdersVisited,                  //عدد طلبيات الزيارات
            'totalAmountOfOrdersVisited'=> $totalAmountOfOrdersVisited,  //المبالغ الاجمالية طلبيات الزيارات
           ];
    }

    public static function exportSalerPlanDetailsStatistic($planId)
    {

    }


    public static function remove_salesman_details($saler_id)
    {
        $workPlan = WorkPlan::where('saler_id', '=', $saler_id)->get()->first();
        if (isset($workPlan)) {
            WorkPlanTask::where('task_plan_id', '=', $workPlan->id)->delete();
            PlanDetails::where('work_plan_id', '=', $workPlan->id)->delete();
            $workPlan->delete();
        }
        DB::delete('delete FROM sales_pharmacy WHERE  sales_id=' . $saler_id . '');
        DB::delete('delete FROM sales_area WHERE  sales_id=' . $saler_id . '');
        DB::delete('delete FROM sales_group WHERE  sales_id=' . $saler_id . '');
    }


    public static function remove_users_details($area_id)
    {
        $users = User::where('area_id', '=', $area_id)->get();
        foreach ($users as $user) {
            if (isset($user->pharmacy)) {
                $user->pharmacy->delete();
                $user->delete();
            } else {
                SalerManager::remove_salesman_details($user->id);
                $user->delete();
            }
        }
    }

    public static function remove_users_details_group($group_id)
    {
        $areas = Area::where('group_id', '=', $group_id)->get();
        foreach ($areas as $area) {
            SalerManager::remove_users_details($area->id);
        }
    }

    public static function remove_users_details_city($city_id)
    {
        $groups = Group::where('city_id', '=', $city_id)->get();
        foreach ($groups as $group) {
            SalerManager::remove_users_details_group($group->id);
        }
    }

    public static function remove_pharmacy_plan($id,$salerId)
    {
        $pharmacyTask = WorkPlanTask::where('pharmacy_id', '=', $id)->get(['id']);
        if (isset($pharmacyTask) && !$pharmacyTask->isEmpty())
            WorkPlanTask::destroy($pharmacyTask->toArray());

        $pharmaciesWork = WorkPlan::where('saler_id','=',$salerId)->get();

        foreach ($pharmaciesWork as $pharmacyWork) {
            $pharmaciesSelectedArray = json_decode($pharmacyWork->pharmacies);

            $result = array_search($id, $pharmaciesSelectedArray);
            if ($result !== false) {
                unset($pharmaciesSelectedArray[$result]);
            }
            $pharmacyWork->pharmacies = json_encode(array_values($pharmaciesSelectedArray));
            $pharmacyWork->save();
        }
    }
}
