<?php

namespace App\Services;

use App\User;
use App\Model\WorkPlan;
use App\Model\WorkPlanTask;
use Carbon\CarbonPeriod;
use Exception;
use function App\CPU\translate;

class WorkPlanServices
{
    

    public static function removePharmaciesTaskPlan($planId)
    {
        try {
            WorkPlanTask::where('task_plan_id', '=', $planId)->delete();
        } catch (Exception $e) {
        }
    }

    public static function checkPharmacyExisting($userAccountNumber)
    {
        try {
            $user = User::where('pharmacy_id', '=', $userAccountNumber)->get()->first();
            if (isset($user))
                return $user->pharmacy->id;
            else
                return false;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function checkPharmacyExistingInPlan($pharmacyId, $planId)
    {
        try {
            $workPlan = WorkPlan::where('id', '=', $planId)->get()->first();
            if (isset($workPlan)) {
                $planPharmacies = json_decode($workPlan->pharmacies, true);
                $result = array_search($pharmacyId, $planPharmacies);
                if ($result !== false)
                    return true;
                else
                    return false;
            } else
                return false;
        } catch (Exception $e) {

            return false;
        }
    }

    public static function checkPharmacyPlanNotReplication($pharmacyId, $planId)
    {
        try {
            $pharmacyPlanTask = WorkPlanTask::where('pharmacy_id', '=', $pharmacyId)
                ->where('task_plan_id', '=', $planId)->get()->first();
            if (!isset($pharmacyPlanTask))
                return true;
            else
                return false;
        } catch (Exception $e) {

            return false;
        }
    }

    public static function checkPreconditionsForImportFile($pharmacyId, $planId)
    {
        try {
            if (
                WorkPlanServices::checkPharmacyExistingInPlan($pharmacyId, $planId) &&
                WorkPlanServices::checkPharmacyPlanNotReplication($pharmacyId, $planId)
            )
                return true;
            else
                return false;
        } catch (Exception $e) {

            return false;
        }
    }

    public static function getDateForDay($day, $planId)
    {
        try {
            $day = trim($day, " \t.");
            $plan = WorkPlan::where(['id' => $planId])->get()->first();
            $periods = CarbonPeriod::create($plan->begin_plan, $plan->end_plan);
            foreach ($periods as $period) {
                $date = $period->format('Y-m-d');
                $dateTima = new \DateTime($date);
                $dayNew = translate($dateTima->format('l'));
                if ($dayNew == $day) {
                    return $date;
                }
            }
        } catch (Exception $e) {
           return $plan->begin_plan;
        }
    }

}
