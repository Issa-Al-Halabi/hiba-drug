<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Brian2694\Toastr\Facades\Toastr;


class DatabaseSettingController extends Controller
{
    public function db_index()
    {
         $tables = [
            0 => "brands",
            1 => "products",
            2 => "stores",
            3 => "banners",
            4 => "orders",
            5 => "bag",
            6 => "support_tickets",
            7 => "visitors",
            8 => "user_import_excel",
            9 => "order_notifications",
        ];
        $rows = [];
        foreach ($tables as $table) {
            $count = DB::table($table)->count();
            array_push($rows, $count);
        }

        return view('admin-views.business-settings.db-index', compact('tables', 'rows'));
    }
    public function clean_db(Request $request)
    {
        $tables = (array)$request->tables;

        if (count($tables) == 0) {
            Toastr::error('No Table Updated');
            return back();
        }

        try {
            DB::transaction(function () use ($tables) {
                foreach ($tables as $table) {
                    if($table=="brands" || $table=="products")
                    {
                        DB::table("products")->delete();
                        DB::table("products_keys")->delete();
                        DB::table("products_points")->delete();
                        DB::table("banners")->delete();
                        DB::table("marketing")->delete();
                        DB::table("wishlists")->delete();
                        DB::table("product_stocks")->delete();
                    }
                    elseif($table=="orders")
                    {
                        DB::table("order_details")->delete();
                        DB::table("bags_orders_details")->delete();
                        DB::table("orders_alameen")->delete();
                    }
                    elseif($table=="bag")
                    {
                        DB::table("products_bag")->delete();
                        DB::table("bags_setting")->delete();
                    }
                    elseif($table=="support_tickets")
                    {
                        DB::table("support_ticket_convs")->delete();
                    }
                    DB::table($table)->delete();
                }
            });
        } catch (\Exception $exception) {
            Toastr::error('Failed to update!');
            return back();
        }


        Toastr::success('Updated successfully!');
        return back();
    }
}
