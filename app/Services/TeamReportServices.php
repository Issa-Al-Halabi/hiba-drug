<?php

namespace App\Services;

use App\Model\Order;
use App\Model\SalerTeam;
use App\Pharmacy;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamReportServices
{
    public static function getTeamsStatistic($dateReportRange): array
    {
        $results = [];
        try {
            $teams = SalerTeam::groupBy('team')->pluck('team');
            for ($i = 0; $i < count($teams); $i++) {

                $query = DB::select('SELECT saler_id FROM salers_teams WHERE team = ?', [$teams[$i]]);
                $salersTeam = array_column($query, 'saler_id');

                $orderCount = Order::whereIn('customer_id', $salersTeam)
                    ->whereBetween('created_at', [$dateReportRange['fromDate'], $dateReportRange['toDate']])
                    ->count();

                $orderPrice = Order::whereIn('customer_id', $salersTeam)
                    ->whereBetween('created_at', [$dateReportRange['fromDate'], $dateReportRange['toDate']])
                    ->sum('order_amount');

                $results[] = [
                    'num_id' => $i + 1,
                    'team' => $teams[$i],
                    'orderCount' => $orderCount,
                    'orderPrice' => $orderPrice,
                    'salersCount' => count($salersTeam),
                ];
            }
            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    public static function getSellersTeamStatistic($dateReportRange, $team): array
    {
        $results = [];
        try {

            $query = DB::select('SELECT saler_id FROM salers_teams WHERE team = ?', [$team]);
            $salersTeam = array_column($query, 'saler_id');
            for ($i = 0; $i < count($salersTeam); $i++) {
                $seler = User::where('id', $salersTeam[$i])->get()->first();

                $orderCount = Order::where('customer_id', $salersTeam[$i])
                    ->whereBetween('created_at', [$dateReportRange['fromDate'], $dateReportRange['toDate']])
                    ->count();

                $orderPrice = Order::where('customer_id', $salersTeam[$i])
                    ->whereBetween('created_at', [$dateReportRange['fromDate'], $dateReportRange['toDate']])
                    ->sum('order_amount');

                $results[] = [
                    'num_id' => $i + 1,
                    'saler_id' => $seler->id,
                    'saler_name' => $seler->name,
                    'orderCount' => $orderCount,
                    'orderPrice' => $orderPrice,
                ];
            }

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }




    public static function getOrdersTeamStatistic($dateReportRange, $team): array
    {
        $results = [];
        try {
            $query = DB::select('SELECT saler_id FROM salers_teams WHERE team = ?', [$team]);
            $salersTeam = array_column($query, 'saler_id');
            for ($i = 0; $i < count($salersTeam); $i++) {
                $seler = User::where('id', $salersTeam[$i])->get()->first();

                $orders = Order::where('customer_id', $salersTeam[$i])
                    ->whereBetween('created_at', [$dateReportRange['fromDate'], $dateReportRange['toDate']])
                    ->where('order_status', 'confirmed')
                    ->get();


                foreach ($orders as $order) {
                    $results[] = [
                        'num_id' => $i + 1,
                        'orderId' => $order->id,
                        'salerName' => $seler->name,
                        'pharmacyName' => $order->pharmacy_name,
                        'orderTotalPrice' => $order->order_amount,
                        'detectionNumber' => $order->Detection_number,
                        'costCenter' =>  $order->cost_center,
                    ];
                }
            }

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }


    public static function getOrdersSellerTeamStatistic($dateReportRange, $sellerId): array
    {
        $results = [];
        try {
            $orders = Order::where('customer_id', $sellerId)
                ->whereBetween('created_at', [$dateReportRange['fromDate'], $dateReportRange['toDate']])
                ->where('order_status', 'confirmed')
                ->get();

            foreach ($orders as $order) {
                $i = 1;

                $results[] = [
                    'num_id' => $i,
                    'orderId' => $order->id,
                    'pharmacyName' => $order->pharmacy_name,
                    'orderTotalPrice' => $order->order_amount,
                    'detectionNumber' => $order->Detection_number,
                    'costCenter' =>  $order->cost_center,
                ];

                $i++;
            }
            return $results;

        } catch (\Exception $e) {
            return [];
        }
    }
}
