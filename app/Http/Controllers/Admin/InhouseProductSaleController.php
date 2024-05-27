<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use App\Model\Category;
use App\Model\Product;
use App\Model\Bag;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Rap2hpoutre\FastExcel\FastExcel;

class InhouseProductSaleController extends Controller
{
    public function index(Request $request)
    {
        $from_date = $request['from_date'];
        $to_date = $request['to_date'];

        $from_date_bag = $request['from_date_bag'];
        $to_date_bag = $request['to_date_bag'];

        $categories = Category::where(['parent_id' => 0])->get();
        $query_param = [
            'category_id' => $request['category_id'],
            'from_date' => $request['from_date'],
            'to_date' => $request['to_date'],
            'from_date_bag' => $request['from_date_bag'],
            'to_date_bag' => $request['to_date_bag'],
        ];

        $products = Product::where(['added_by' => 'admin'])
            ->when($request->has('category_id') && $request['category_id'] != 'all', function ($query) use ($request) {
                $query->whereJsonContains('category_ids', [[['id' => (string)$request['category_id']]]]);
            })
            ->with(['order_details']);

        if (!is_null($from_date)) {
            $products->whereDate("created_at", ">=", Carbon::parse($from_date));
        }
        if (!is_null($to_date)) {
            $products->whereDate("created_at", "<=", Carbon::parse($to_date));
        }
        $products = $products->paginate(Helpers::pagination_limit(), ['*'], 'product-page')
            ->appends($query_param);

        $category_id = $request['category_id'];


        $bags = Bag::with(['bag_order_details']);

        if (!is_null($from_date_bag)) {
            $bags->whereDate("created_at", ">=", Carbon::parse($from_date_bag));
        }
        if (!is_null($to_date_bag)) {
            $bags->whereDate("created_at", "<=", Carbon::parse($to_date_bag));
        }

        $bags = $bags->paginate(Helpers::pagination_limit(), ['*'], 'bag-page')
            ->appends($query_param);

        return view('admin-views.report.inhouse-product-sale', compact('categories', 'category_id', 'products', 'bags', 'from_date', 'to_date', 'from_date_bag', 'to_date_bag'));
    }

    public function generateExcel(Request $request)
    {
        $from_date = $request['from_date'];
        $to_date = $request['to_date'];

        $products = Product::where(['added_by' => 'admin'])
            ->when($request->has('category_id') && $request['category_id'] != 'all', function ($query) use ($request) {
                $query->whereJsonContains('category_ids', [[['id' => (string)$request['category_id']]]]);
            })
            ->with(['order_details']);

        if (!is_null($from_date)) {
            $products->whereDate("created_at", ">=", Carbon::parse($from_date));
        }
        if (!is_null($to_date)) {
            $products->whereDate("created_at", "<=", Carbon::parse($to_date));
        }
        $products = $products->get();

        $excel = [];

        $excel[] = [
            "#SL" => "#SL",
            \App\CPU\translate('Product Name') => \App\CPU\translate('Product Name'),
            \App\CPU\translate('Total') => \App\CPU\translate('Total'),
            \App\CPU\translate('Total Sale offers') => \App\CPU\translate('Total Sale offers'),
            \App\CPU\translate('Total Price') => \App\CPU\translate('Total Price'),
        ];

        foreach ($products as $key => $data) {
            $Totalprice = 0;

            foreach ($data->order_delivered as $order) {

                $Totalprice += $order->qty * $order->price;
            }

            $excel[] = [
                "#SL" => $key + 1,
                \App\CPU\translate('Product Name') =>  $data['name'],
                \App\CPU\translate('Total') => $data->order_delivered->sum('qty'),
                \App\CPU\translate('Total Sale offers') => $data->order_delivered_offers->sum('total_qty'),
                \App\CPU\translate('Total Price') => $Totalprice,
            ];
        }

        $now = Carbon::now()->format("Y_m_d");
        $fileName = \App\CPU\translate('InHouse product sale report') . "_" . $now;
        return (new FastExcel($excel))
            ->withoutHeaders()
            ->download($fileName . '.xlsx');
    }

    public function generateExcelBag(Request $request)
    {
        $from_date_bag = $request['from_date_bag'];
        $to_date_bag = $request['to_date_bag'];

        $bags = Bag::with(['bag_order_details']);

        if (!is_null($from_date_bag)) {
            $bags->whereDate("created_at", ">=", Carbon::parse($from_date_bag));
        }
        if (!is_null($to_date_bag)) {
            $bags->whereDate("created_at", "<=", Carbon::parse($to_date_bag));
        }

        $bags = $bags->get();

        $excel = [];

        $excel[] = [
            "#SL" => "#SL",
            \App\CPU\translate('Bag Name') => \App\CPU\translate('Bag Name'),
            \App\CPU\translate('Total Sale') => \App\CPU\translate('Total Sale'),
            \App\CPU\translate('Total Price') => \App\CPU\translate('Total Price'),
        ];

        foreach ($bags as $key => $bag) {
            $BagTotalprice = 0;
            foreach ($bag->bag_order_delivered as $bag_order) {
                $BagTotalprice += $bag_order->bag_qty * $bag_order->bag_price;
            }
            $excel[] = [
                "#SL" => $key + 1,
                \App\CPU\translate('Bag Name') => $bag['bag_name'],
                \App\CPU\translate('Total Sale') => $bag->bag_order_delivered->sum('bag_qty'),
                \App\CPU\translate('Total Price') =>  $BagTotalprice,
            ];
        }

        $now = Carbon::now()->format("Y_m_d");
        $fileName = \App\CPU\translate('InHouse bag sale report') . "_" . $now;
        return (new FastExcel($excel))
            ->withoutHeaders()
            ->download($fileName . '.xlsx');
    }
}
