<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Model\WorkPlan;
use App\Model\PlanDetails;
use App\Model\PlanArchive;
use App\Pharmacy;
use App\User;
use App\Model\WorkPlanTask;
use App\CPU\Helpers;
use App\CPU\SalerManager;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Model\Order;
use App\Model\SalerTeam;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Traits\distanceTrait;
use Carbon\CarbonPeriod;
use Carbon\Carbon;
use App\Services\WorkPlanServices;

class WorkPlanController extends Controller
{
    use distanceTrait;
    public function work_plans_list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $plans = WorkPlan::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('saler_name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $plans = WorkPlan::whereNotIn('id', [-2]);
        }
        $plans = $plans->latest()->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.work-plan.list', compact('search', 'plans'));
    }

    public function work_plan_add()
    {
        $salerSelected = WorkPlan::get(['saler_id']);
        $salesman = User::where('user_type', '=', 'salesman')->whereNotIn('id', $salerSelected)->get();
        return view('admin-views.work-plan.add', compact('salesman'));
    }

    public function work_plan_store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'begin_date' => 'required',
            'note' => 'required',
            'saler_id' => 'required',
            'pharamcies_ids' => 'required',
        ], [
            'pharamcies_ids.required' => 'pharamcies  is required!',
        ]);

        if ($request->has('check_all')) {
            $pharma_id  = DB::select('select pharmacy_id from sales_pharmacy where sales_id = ?', [$request->saler_id]);
            $arr = array();
            foreach ($pharma_id as $idx) {
                array_push($arr, (string)$idx->pharmacy_id);
            }
            if ($arr == null || count($arr) == 0) {
                return back();
            }
            $pharmacies = json_encode($arr);
        } else {
            $pharmacies = json_encode($request->pharamcies_ids);
        }

        $endDate = date('Y-m-d', strtotime($request->begin_date . ' + 6 days'));
        $saler = User::where('id', '=', $request->saler_id)->get()->first();
        DB::table('salers_work_plans')->insert([
            'begin_plan' => $request->begin_date,
            'end_plan' => $endDate,
            'saler_id' => $request->saler_id,
            'note' => $request->note,
            'saler_name' => $saler->name,
            'pharmacies' => $pharmacies,
            'status_plan' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Toastr::success('Plan added successfully!');
        return back();
    }

    public function work_plan_delete($id)
    {
        $workPlan = WorkPlan::find($id);
        $workPlanTasks = WorkPlanTask::where('task_plan_id', '=', $id);
        $planDetails = PlanDetails::where('work_plan_id', '=', $id);

        if (isset($workPlan) && isset($workPlanTasks)) {
            $workPlanTasks->delete();
            $planDetails->delete();
            $workPlan->delete();
        }
        Toastr::success(translate('Work plan removed!'));
        return back();
    }

    public function work_plan_edit($id)
    {
        $plan = WorkPlan::where(['id' => $id])->withoutGlobalScopes()->first();
        $salesman = User::where('user_type', '=', "salesman")->get();


        $pharma_id  = DB::select('select pharmacy_id from sales_pharmacy where sales_id = ?', [$plan->saler_id]);
        $arr = array();
        foreach ($pharma_id as $idx) {
            array_push($arr, $idx->pharmacy_id);
        }

        $pharmaciesSelectedArray = json_decode($plan->pharmacies, true);
        $pharmacies = Pharmacy::whereIn('id', $arr)->get();

        return view('admin-views.work-plan.edit', compact('plan', 'salesman', 'pharmacies', 'pharmaciesSelectedArray'));
    }

    public function work_plan_update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'begin_date' => 'required',
            'end_date' => 'required',
            'note' => 'required',
            'saler_id' => 'required',
            'pharamcies_ids' => 'required',
        ], [
            'pharamcies_ids.required' => 'pharamcies  is required!',
        ]);

        $pharmacies = json_encode($request->pharamcies_ids);
        $saler = User::where('id', '=', $request->saler_id)->get()->first();

        $plan = WorkPlan::where(['id' => $id])->withoutGlobalScopes()->first();
        $plan->begin_plan = $request->begin_plan;
        $plan->end_plan = $request->end_plan;
        $plan->saler_id = $request->saler_id;
        $plan->note = $request->note;
        $plan->saler_name = $saler->name;
        $plan->pharmacies = $pharmacies;
        $plan->status_plan = 1;
        $plan->save();
        Toastr::success('Plan updated successfully!');
        return back();
    }

    public function work_plan_activation($id)
    {
    }

    public function work_plan_pharmacies($saler_id)
    {
        $sm = User::where(['id' => $saler_id])->first();
        $pharma_id  = DB::select('select pharmacy_id from sales_pharmacy where sales_id = ?', [$saler_id]);
        $arr = array();
        foreach ($pharma_id as $idx) {
            array_push($arr, $idx->pharmacy_id);
        }
        $pharmacies = Pharmacy::whereIn('id', $arr)->get();

        return response()->json([
            'pharmacies' => $pharmacies
        ]);
    }


    public function work_plan_details($plan_id, Request $request)
    {
        $search = "";
        $countVisitedMatchSite=0;
        $workPlan = WorkPlan::find($plan_id);
        $fromDate=(isset($request->fromDate)) ?  $request->fromDate : $workPlan->begin_plan;
        $toDate=(isset($request->toDate)) ?  $request->toDate : $workPlan->end_plan;
        $datePlanRange=[
            'fromDate'=>$fromDate,
            'toDate'=>$toDate,
        ];

        try {
            $PharmaciesPlan = PlanDetails::where('work_plan_id', '=', $plan_id)
            ->whereBetween('visit_time', [$fromDate, $toDate])
            ->get();
            foreach ($PharmaciesPlan as $c) {
                $area = " ";
                $site_match = 0;
                $street_address = " ";
                $pharmacy = Pharmacy::where('id', '=', $c->Wpharmacy_id)->get()->first();
                $res = $this->get_location($c->Wlat, $c->Wlng);
                $c['pharmacy_name'] = $pharmacy->name;
                $c['area'] = $res["area"];
                $c['street_address'] = $res["street"];
                $c['site_match'] = $this->site_match($c->Wlat, $c->Wlng, $pharmacy->lat, $pharmacy->lan);
                ($c['site_match']==1) ?  $countVisitedMatchSite++ : '';
            }
            $statistics=SalerManager::getSalerPlanDetailsStatistic($plan_id,$fromDate,$toDate,$workPlan->saler_id);
            $statistics['countVisitedMatchSite']=$countVisitedMatchSite;
        } catch (Exception $e) {
            return redirect('admin/sales-man/work-plans/list');
        }
        return view('admin-views.work-plan.details', compact('PharmaciesPlan', 'search','statistics','workPlan','datePlanRange'));
    }


    public function get_location($lat, $lng)
    {

        $res = array();
        try {
            $apikey = 'AIzaSyCPsxZeXKcSYK1XXw0O0RbrZiI_Ekou5DY';
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=$lat,$lng&key=$apikey";
            $header = array(
                "authorization: key=" . $apikey . "",
                "content-type: application/json"
            );
            $ch = curl_init();
            $timeout = 120;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            // Get URL content
            $result = curl_exec($ch);

            $result = json_decode($result);
            // close handle to release resources
            curl_close($ch);

            return $res = [
                'area' => $result->results[0]->address_components[2]->long_name,
                'street' => $result->results[0]->address_components[1]->long_name
            ];
        } catch (Exception $e) {
            return $res = [
                'area' => " ",
                'street' => " "
            ];
        }
    }


    public function site_match($latSite, $lngSite, $latPharmacy, $lngPharmacy)
    {
        $result = 0;
        $Start_distance = $this->distance($latSite, $lngSite, $latPharmacy, $lngPharmacy, "K");
        if ($Start_distance <= 0.1)
            $result = 1;
        else
            $result = 0;
        return $result;
    }

    public function work_plan_tasks(Request $request, $id)
    {
        $plan = WorkPlan::where(['id' => $id])->withoutGlobalScopes()->first();
        $plan_id = $plan->id;

        $pharmaciesSelected = json_decode($plan->pharmacies, true);
        $pharmaciesSelectedTasks = WorkPlanTask::where([['task_plan_id', '=', $plan_id]])->get(['pharmacy_id']);
        $pharmacies = Pharmacy::whereIn('id', $pharmaciesSelected)
            ->whereNotIn('id', $pharmaciesSelectedTasks)
            ->get();

        //Pharmacies for new insert to work plan
        $salerPharmacies = DB::select('select pharmacy_id from sales_pharmacy where sales_id = ?', [$plan->saler_id]);
        $salerPharmaciesId = array();
        foreach ($salerPharmacies as $id) {
            array_push($salerPharmaciesId, $id->pharmacy_id);
        }
        $pharmaciesNew = Pharmacy::orderBy('id', 'DESC')
            ->whereIn('id', $salerPharmaciesId)
            ->where(function ($query)  use ($pharmaciesSelected) {
                $query->whereNotIn('id', $pharmaciesSelected);
            })->get();
        //End

        $periods = CarbonPeriod::create($plan->begin_plan, $plan->end_plan);
        $begin = $this->rev_date($plan->begin_plan);
        $end = $this->rev_date($plan->end_plan);

        return view('admin-views.work-plan.tasks', compact('pharmacies', 'pharmaciesNew', 'periods', 'plan_id', 'begin', 'end'));
    }

    public function rev_date($date)
    {
        $array = explode("-", $date);
        $rev = array_reverse($array);
        $date = implode("-", $rev);
        return $date;
    }

    public function work_plan_task_store(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pharmacy_id' => 'required',
            'task_date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['Error' => 'Data is faild added']);
        }

        try {

            $task = WorkPlanTask::where([
                ['task_plan_id', '=', $id],
                ['pharmacy_id', '=', $request->pharmacy_id]
            ])->get()->first();

            if (isset($task) && $request->task_date == "pharmacies") {
                $task->delete();
                return response()->json(['success' => 'Data is successfully added']);
            }


            if (!isset($task) && $request->task_date == "pharmacies") {
                return response()->json(['success' => 'Data is successfully added']);
            }


            if (isset($task)) {
                if ($task->task_date != $request->task_date) {
                    $task->task_date = $request->task_date;
                    $task->update();
                }
            } else {
                $task = new WorkPlanTask;
                $task->task_date = $request->task_date;
                $task->task_plan_id = $id;
                $task->pharmacy_id = $request->pharmacy_id;
                $task->save();
            }

            return response()->json(['success' => 'Data is successfully added']);
        } catch (Exception $e) {
            return response()->json(['Error' => 'Data is faild added']);
        }
    }

    public function plan_set_date(Request $request)
    {
        $from = $request['plan_from_date'];
        $to = $request['plan_to_date'];
        $team_char = $request['plan_team_char'];

        session()->put('plan_from_date', $from);
        session()->put('plan_to_date', $to);
        session()->put('plan_team_char', $team_char);
      

        $previousUrl = strtok(url()->previous(), '?');
      
        return redirect()->to($previousUrl . '?' . http_build_query(['from_date' => $request['plan_from_date'], 'to_date' => $request['plan_to_date'], 'team' => $request['plan_team_char']]))->with(['from' => $from, 'to' => $to]);
    }
  
  
    public function generate_excel_report()
    {

        if (session()->has('plan_from_date') == false) {
            session()->put('plan_from_date', date('Y-m-01'));
            session()->put('plan_to_date', date('Y-m-30'));
        }
        if (session()->has('plan_team_char') == false) {
            session()->put('plan_team_char', 'A');
        }
        $plan_from_date = session('plan_from_date');
        $plan_to_date = session('plan_to_date');
        $plan_team_char = session('plan_team_char');

        $plansArchives = PlanArchive::whereBetween('begin_date', [$plan_from_date, $plan_to_date])
            ->where('team_name', '=', $plan_team_char)
            ->paginate(5, ['*'], 'page');

        $excel = [];
          $excel[] = [
            "بداية الخطة" =>"بداية الخطة",
            "نهاية الخطة" =>"نهاية الخطة",
            "اسم المندوب" =>"اسم المندوب",
            "عدد الزيارات" =>"عدد الزيارات",
            "عدد الصيدلات" =>"عدد الصيدلات",
            "عدد الطلبيات" =>"عدد الطلبيات",
        ];
        foreach ($plansArchives as $key => $planArchive) {
            $excel[] = [
                "بداية الخطة" => $planArchive->begin_date,
                "نهاية الخطة" => $planArchive->end_date,
                "اسم المندوب" => $planArchive->saler_name,
                "عدد الزيارات" => $planArchive->pharmancies_visit_num,
                "عدد الصيدلات" => $planArchive->pharmacies_num,
                "عدد الطلبيات" => $planArchive->orders_num,
            ];
        }
        $fileName = "تقارير الخطط " . $plan_from_date . "_" . $plan_to_date;
        return (new FastExcel($excel))
            ->withoutHeaders()
            ->download($fileName . '.xlsx');
    }

    public function work_plans_report()
    {

        if (session()->has('plan_from_date') == false) {
            session()->put('plan_from_date', date('Y-m-01'));
            session()->put('plan_to_date', date('Y-m-30'));
        }
        if (session()->has('plan_team_char') == false) {
            session()->put('plan_team_char', 'A');
        }
        $plan_from_date = session('plan_from_date');
        $plan_to_date = session('plan_to_date');
        $plan_team_char = session('plan_team_char');

        $plansArchive = PlanArchive::whereBetween('begin_date', [$plan_from_date, $plan_to_date])
            ->where('team_name', '=', $plan_team_char)
            ->paginate(5, ['*'], 'page');

        $selerIds = SalerTeam::where('team', $plan_team_char)->get(['saler_id']);

        //Total orders
        $total = Order::where('order_type', 'default_type')
            ->where('customer_type', 'salesman')
            ->whereIn('customer_id', $selerIds)
            ->whereBetween('created_at', [$plan_from_date, $plan_to_date])
            ->count();

        //Total delivered
        $delivered = $this->get_report_salesman($plan_from_date, $plan_to_date, "delivered", $selerIds);
        //Total returned
        $returned = $this->get_report_salesman($plan_from_date, $plan_to_date, "returned", $selerIds);
        //Total failed
        $failed = $this->get_report_salesman($plan_from_date, $plan_to_date, "failed", $selerIds);
        //Total processing
        $processing = $this->get_report_salesman($plan_from_date, $plan_to_date, "processing", $selerIds);
        //Total canceled
        $canceled = $this->get_report_salesman($plan_from_date, $plan_to_date, "canceled", $selerIds);

        if ($total == 0) {
            $totalRange = .01;
        } else {
            $totalRange = $total;
        }

        $data = [
            'total' => $total,
            'totalRange' => $totalRange,
            'delivered' => $delivered,
            'returned' => $returned,
            'failed' => $failed,
            'processing' => $processing,
            'canceled' => $canceled
        ];

        return view('admin-views.work-plan.report', compact('plansArchive', 'data'));
    }

    public function get_report_salesman($plan_from_date, $plan_to_date, $status, $selerIds)
    {
        $data = Order::where('order_type', 'default_type')
            ->where(['order_status' => $status, 'customer_type' => 'salesman'])
            ->whereIn('customer_id', $selerIds)
            ->whereBetween('created_at', [$plan_from_date, $plan_to_date])
            ->count();
        return $data;
    }

    public function plan_details_report(Request $request, $id)
    {
        $subLaws = '';
        $total_price = 0;
        $total_orders = 0;
        $planArchive = PlanArchive::where('id', '=', $id)->get()->first();

        $orders = Order::where('order_type', 'default_type')
            ->where('customer_id', $planArchive->saler_id)
            ->where('order_type', '=', 'default_type')
            ->whereBetween('created_at', [$planArchive->begin_date, $planArchive->end_date])
            ->get();

        $total_price = $orders->sum('order_amount');
        $total_orders = $orders->count();

        foreach ($orders as $order) {
            $subLaws .= '<tr class="odd">
            <td>' . $order['id'] . '</td>
            <td>' . $order['payment_status'] . '</td>
            <td>' . $order['order_status'] . '</td>
            <td>' . $order['order_amount'] . '</td>
            <td>' . $order['created_at'] . '</td>
            <tr>';
        }

        return response()->json([
            'data' => $subLaws,
            'total_price' => $total_price,
            'total_orders' => $total_orders
        ]);
    }

      public function work_plan_refresh(Request $request, $plan_id)
    {
        try {
            DB::beginTransaction();

            $plan = WorkPlan::where('id', '=', $plan_id)->get()->first();
            //get details for archive
            $num_visited = PlanDetails::where('work_plan_id', '=', $plan_id)->where('visited', '=', 1)->count();

            $num_orders = Order::where('order_type', 'default_type')
                ->where('customer_id', $plan->saler_id)
                ->where('order_type', '=', 'default_type')
                ->whereBetween('created_at', [$plan->begin_plan, $plan->end_plan])
                ->get()->count();
            $saler = User::where('id', '=', $plan->saler_id)->get()->first();
            $team = SalerTeam::where('saler_id', '=', $plan->saler_id)->get()->first();



            //refresh date plan
            $begin = date("Y-m-d", strtotime(Carbon::createFromFormat('Y-m-d', $plan->begin_plan)->addDays(7)));
            $end = date("Y-m-d", strtotime(Carbon::createFromFormat('Y-m-d', $plan->end_plan)->addDays(7)));
            $plan->begin_plan = $begin;
            $plan->end_plan = $end;
            $plan->save();

            $workPlanTask = WorkPlanTask::where('task_plan_id', '=', $plan_id)->get();
            foreach ($workPlanTask as $task) {
                $date = date("Y-m-d", strtotime(Carbon::createFromFormat('Y-m-d', $task->task_date)->addDays(7)));
                $task->task_date = $date;
                $task->save();
            }

             //Archive plan
             $planArchive = new PlanArchive;
             $planArchive->begin_date = $plan->begin_plan;
             $planArchive->end_date = $plan->end_plan;
             $planArchive->team_name = $team->team;
             $planArchive->saler_id = $plan->saler_id;
             $planArchive->saler_name = $saler->name;
             $planArchive->pharmancies_visit_num = $num_visited;
             $planArchive->orders_num = $num_orders;
             $planArchive->pharmacies_num = $workPlanTask->count();

             $planArchive->save();

            //delete plan details
            PlanDetails::where('work_plan_id', '=', $plan_id)->delete();

            DB::commit();
            Toastr::success('Refresh successfully!');
            return back();
        } catch (\Throwable $th) {
            Toastr::success('Refresh faild!');
            return back();
        }
    }
  
    public function plan_archive_remove($id)
    {
        $planArchive = PlanArchive::where('id', '=', $id)->get()->first();
        $planArchive->delete();
        Toastr::success(translate('plan removed!'));
        return back();
    }

    public function insert_pharmacy_plan(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'plan_id' => 'required',
                'pharmacy_id' => 'required',
            ]);
            if ($validator->fails()) {
                Toastr::error('Faild Insert!');
            }
            DB::transaction(function () use ($request) {
                $workPlan = WorkPlan::where('id', '=', $request->plan_id)->get()->first();
                $pharmaciesWorkPlan = json_decode($workPlan->pharmacies);
                array_push($pharmaciesWorkPlan, $request->pharmacy_id);
                $workPlan->pharmacies = json_encode($pharmaciesWorkPlan);
                $workPlan->save();
            });
            Toastr::success('Insert pharmacy successfully!');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Faild Insert!');
            return back();
        }
    }

    public function plan_task_import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['Error' => 'Data is faild added']);
        }

        try {
            $collections = (new FastExcel)->import($request->file('tasks_file'));
        } catch (\Exception $exception) {
            Toastr::error('You have uploaded a wrong format file, please upload the right file.');
            return back();
        }

        $skip = ['اليوم', 'اسم الصيدلية', 'رمز الحساب'];
        DB::beginTransaction();
        try {
            WorkPlanServices::removePharmaciesTaskPlan($request->plan_id);
            foreach ($collections as $collection) {

                foreach ($collection as $key => $value) {
                    if ($key != "" && $value === "" && !in_array($key, $skip)) {
                        Toastr::error('Please fill ' . $key . ' fields');
                        return back();
                    }
                }
                //$accountNumber = trim($collection['رمز الحساب'] , " \t.");
                $pharmacyId = WorkPlanServices::checkPharmacyExisting($collection['رمز الحساب']);
                if ($pharmacyId != false) {
                    if (WorkPlanServices::checkPreconditionsForImportFile($pharmacyId, $request->plan_id)) {
                        $planTask = new WorkPlanTask;
                        $planTask->task_plan_id = $request->plan_id;
                        $planTask->pharmacy_id = $pharmacyId;
                        $planTask->task_date = WorkPlanServices::getDateForDay($collection['اليوم'], $request->plan_id);
                        $planTask->save();
                    }
                }
            }
            DB::commit();
            Toastr::success('Tasks imported successfully!');
            return back();
        } catch (\Exception $e) {
            DB::rollback();
            Toastr::success('Tasks imported faild!');
            return back();
            // something went wrong
        }
    }
}
