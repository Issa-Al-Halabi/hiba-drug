<?php

namespace App\Http\Controllers\Admin;

use App\Services\TeamReportServices;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

use \Yajra\Datatables\Datatables;
use Exception;

class SalesManReportController extends Controller
{
    public function getTeamReport(Request $request)
    {
        try {
            $fromDate = (isset($request->fromDate)) ?  $request->fromDate : Carbon::now()->startOfWeek();
            $toDate = (isset($request->toDate)) ?  $request->toDate : Carbon::now()->endOfWeek();
            $dateReportRange = [
                'fromDate' => $fromDate,
                'toDate' => $toDate,
            ];
            $resultsTeamsReport = TeamReportServices::getTeamsStatistic($dateReportRange);
            $resultsTeamsReport = array_map(function ($result) {
                return (object) $result;
            }, $resultsTeamsReport);
        } catch (Exception $e) {
            return back();
        }
        return view('admin-views.sales-man.report.team', compact('dateReportRange', 'resultsTeamsReport'));
    }



    public function getSellersOfTeamReport(Request $request, $team)
    {
        try {
            $fromDate = (isset($request->fromDate)) ?  $request->fromDate : Carbon::now()->startOfWeek();
            $toDate = (isset($request->toDate)) ?  $request->toDate : Carbon::now()->endOfWeek();
            $dateReportRange = [
                'fromDate' => $fromDate,
                'toDate' => $toDate,
            ];
            $sellersTeamReport = TeamReportServices::getSellersTeamStatistic($dateReportRange, $team);
            $sellersTeamReport = array_map(function ($result) {
                return (object) $result;
            }, $sellersTeamReport);
        } catch (Exception $e) {
            return back();
        }
        return view('admin-views.sales-man.report.sellersTeam', compact('dateReportRange', 'sellersTeamReport', 'team'));
    }

    public function getOrdersTeamReport(Request $request, $team)
    {
        try {
            $fromDate = (isset($request->fromDate)) ?  $request->fromDate : Carbon::now()->startOfWeek();
            $toDate = (isset($request->toDate)) ?  $request->toDate : Carbon::now()->endOfWeek();
            session()->put('from_date', $fromDate);
            session()->put('to_date', $toDate);
            $dateReportRange = [
                'fromDate' => session()->get('from_date'),
                'toDate' => session()->get('to_date'),
            ];
            return view('admin-views.sales-man.report.ordersTeam', compact('dateReportRange', 'team'));
        } catch (Exception $e) {
            return back();
        }
    }

    //TODO ajax
    public function getOrdersTeamReportAjax(Request $request, $team)
    {
        $resultsOrdersTeamReport = [];
        try {
            $dateReportRange = [
                'fromDate' => session()->get('from_date'),
                'toDate' => session()->get('to_date'),
            ];
            if ($request->ajax()) {
                $resultsOrdersTeamReport = TeamReportServices::getOrdersTeamStatistic($dateReportRange, $team);
                $resultsOrdersTeamReport = array_map(function ($result) {
                    return (object) $result;
                }, $resultsOrdersTeamReport);
                return Datatables::of($resultsOrdersTeamReport)->make(true);
            }
        } catch (Exception $e) {
            return back();
        }
    }

    public function getOrdersSellerOfTeamReport(Request $request, $sellerId)
    {
        try {
            $fromDate = (isset($request->fromDate)) ?  $request->fromDate : Carbon::now()->startOfWeek();
            $toDate = (isset($request->toDate)) ?  $request->toDate : Carbon::now()->endOfWeek();
            $dateReportRange = [
                'fromDate' => $fromDate,
                'toDate' => $toDate,
            ];
            $seller = User::where('id', $sellerId)->get()->first();
            $ordersSellerTeamReport = TeamReportServices::getOrdersSellerTeamStatistic($dateReportRange, $sellerId);
            $ordersSellerTeamReport = array_map(function ($result) {
                return (object) $result;
            }, $ordersSellerTeamReport);
        } catch (Exception $e) {
            return back();
        }
        return view('admin-views.sales-man.report.ordersSeller', compact('dateReportRange', 'ordersSellerTeamReport', 'seller'));
    }


}
