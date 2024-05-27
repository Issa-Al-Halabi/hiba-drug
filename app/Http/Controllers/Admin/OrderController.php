<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\OrderManager;
use App\Services\OrderServices;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Model\Bag;
use App\User;
use App\Model\DeliveryMan;
use App\Model\Order;
use App\Model\OrderDetail;
use App\Model\OrderTransaction;
use App\Model\Product;
use App\Model\Seller;
use App\Model\BagProduct;
use App\Model\BagsOrdersDetails;
use App\Model\Brand;
use App\Model\ShippingAddress;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use function App\CPU\translate;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\DB;
use App\Pharmacy;
use App\Services\BagServices;
use Exception;
use Illuminate\Support\Carbon;


class OrderController extends Controller
{

    public function list(Request $request, $status)
    {
      
        $query_param = [];
        $search = $request['search'];
        $customer_type = "all";
        $customer_id = 0;
        $from_date = null;
        $to_date = null;

        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            $query = Order::whereHas('details', function ($query) {
                $query->whereHas('product', function ($query) {
                    $query->where('added_by', 'admin');
                });
            })->with(['customer']);

            if ($status != 'all') {
                $orders = $query->where(['order_status' => $status]);
            } else {
                $orders = $query;
            }
        } else {
            if ($status != 'all') {
                $orders = Order::with(['customer', 'pharmacy'])->where(['order_status' => $status]);
            } else {
                $orders = Order::with(['customer', 'pharmacy']);
            }
        }
        Order::where(['checked' => 0])->update(['checked' => 1]);


        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $orders = $orders->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_ref', 'like', "%{$value}%")
                        ->orWhere('pharmacy_name', 'like', "%{$value}%");
                }
            });
            $query_param['search'] =  $request['search'];
        }


        if ($request->has('customer_type')) {
            $customer_type = $request['customer_type'];
            if ($request['customer_type'] != 'all') {
                $key = explode(' ', $request['customer_type']);
                $orders = $orders->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('customer_type', 'like', "%{$value}%");
                    }
                });
              $query_param['customer_type'] =  $request['customer_type'];
            }
        }

        if ($request->has('customer_id') && $request->customer_id != 0) {
            $customer_id = $request['customer_id'];
            $key = explode(' ', $request['customer_id']);
            $orders = $orders->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('customer_id', 'like', "%{$value}%");
                }
            });
          $query_param['customer_id'] =  $request['customer_id'];
        }
      
      if ($request->has('from_date')&&$request['from_date']!=null) {
            $from_date = $request['from_date'];
            $orders = $orders->whereDate("created_at",">=",Carbon::parse($from_date));
        	$query_param['from_date'] =  $request['from_date'];
        }
      
      if ($request->has('to_date') && $request['to_date']!=null) {
            $to_date = $request['to_date'];
            $orders = $orders->whereDate("created_at","<=",Carbon::parse($to_date));
          	$query_param['to_date'] =  $request['to_date'];
      }
      
        $orders = $orders->where('order_type', 'default_type')
            ->orderBy('id', 'desc')->paginate(Helpers::pagination_limit())
            ->appends($query_param);

        $salers = User::where('user_type', '=', 'salesman')->get();

      	return view('admin-views.order.list', compact('orders', 'status', 'search', 'customer_type', 'salers', 'customer_id', 'from_date', 'to_date'));
    }
  

    
    public function generate_excel_report(Request $request, $status)
    {

        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            $query = Order::whereHas('details', function ($query) {
                $query->whereHas('product', function ($query) {
                    $query->where('added_by', 'admin');
                });
            })->with(['customer']);

            if ($status != 'all') {
                $orders = $query->where(['order_status' => $status]);
            } else {
                $orders = $query;
            }
        } else {
            if ($status != 'all') {
                $orders = Order::with(['customer', 'pharmacy'])->where(['order_status' => $status]);
            } else {
                $orders = Order::with(['customer', 'pharmacy']);
            }
        }
        Order::where(['checked' => 0])->update(['checked' => 1]);


        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $orders = $orders->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('order_status', 'like', "%{$value}%")
                        ->orWhere('transaction_ref', 'like', "%{$value}%")
                        ->orWhere('pharmacy_name', 'like', "%{$value}%");
                }
            });
        }


        if ($request->has('customer_type')) {
            if ($request['customer_type'] != 'all') {
                $key = explode(' ', $request['customer_type']);
                $orders = $orders->where(function ($q) use ($key) {
                    foreach ($key as $value) {
                        $q->orWhere('customer_type', 'like', "%{$value}%");
                    }
                });
            }
        }

        if ($request->has('customer_id') && $request->customer_id != 0) {
            $key = explode(' ', $request['customer_id']);
            $orders = $orders->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('customer_id', 'like', "%{$value}%");
                }
            });
        }

          if ($request->has('from_date')&&$request['from_date']!=null) {
            $orders = $orders->whereDate("created_at", ">=", Carbon::parse($request['from_date']));
        }

         if ($request->has('to_date')&&$request['to_date']!=null) {
            $orders = $orders->whereDate("created_at", "<=", Carbon::parse($request['to_date']));
        }

        $orders = $orders->where('order_type', 'default_type')
            ->orderBy('id', 'desc')->get();


        $excel = [];
        $excel[] = [
            "#SL" => "#SL",
            "طلبية" => "طلبية",
            "تاريخ" => "تاريخ",
            "اسم الصيدلية" => "اسم الصيدلية",
            "اسم الزبون" => "اسم الزبون",
            "نوع الزبون" => "نوع الزبون",
            "الحالة" => "الحالة",
            "المجموع" => "المجموع",
            "حالة الطلبية" => "حالة الطلبية",
        ];
        foreach ($orders as $key => $order) {

            $excel[] = [
                "#SL" => $key,
                "طلبية" =>  $order->id,
                "تاريخ" => $order->created_at->format("Y-m-d"),
                "اسم الصيدلية" => $order->pharmacy != null ? $order->pharmacy->name  : \App\CPU\translate('invalid_customer_data'),
                "اسم الزبون" => $order->customer != null ? $order->customer->name : \App\CPU\translate('invalid_customer_data'),
                "نوع الزبون" => $order->customer != null ? \App\CPU\translate($order->customer->user_type) : \App\CPU\translate('invalid_customer_data'),
                "الحالة" => $order->payment_status == 'paid' ? \App\CPU\translate('paid') : \App\CPU\translate('unpaid'),
                "المجموع" =>  \App\CPU\BackEndHelper::set_symbol(\App\CPU\BackEndHelper::usd_to_currency($order->order_amount)),
                "حالة الطلبية" => \App\CPU\translate($order->order_status),
            ];
        }
        $now = Carbon::now()->format("Y_m_d");
        $fileName = "تقارير الطلبيات " . \App\CPU\translate($status) . "_" . $now;
        return (new FastExcel($excel))
            ->withoutHeaders()
            ->download($fileName . '.xlsx');
    }
  
    public function details($id)
    {
        $order = Order::with('details', 'shipping', 'seller')->where(['id' => $id])->first();
        $bagsOrder = BagsOrdersDetails::where(['order_id' => $id])->get();

        $count = 0;
        foreach ($bagsOrder as $bagOrder) {
            $bag = Bag::where(['id' => $bagOrder->bag_id])->get()->first();
            $bagProducts = BagProduct::where(['bag_id' => $bagOrder->bag_id])->get();
            foreach ($bagProducts as $bagProduct) {
                if ($bagProduct->is_gift == 1) {
                    $count = $count + $bagProduct->product_count;
                }
            }
            $bagOrder['bag_name'] = (isset($bag->bag_name)) ? $bag->bag_name : "none";
            $bagOrder['total_qty'] = $count;
        }

        $shipping_method = Helpers::get_business_settings('shipping_method');
        $delivery_men = DeliveryMan::where('is_active', 1)->when($order->seller_is == 'admin', function ($query) {
            $query->where(['seller_id' => 0]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'sellerwise_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => $order['seller_id']]);
        })->when($order->seller_is == 'seller' && $shipping_method == 'inhouse_shipping', function ($query) use ($order) {
            $query->where(['seller_id' => 0]);
        })->get();


        $customerDetails = User::where('id', $order->customer_id)->get()->first();

        $status = false;
        if ($order->orderBy_id != null && $order->orderBy_id != 0) {

            $pharmacy = Pharmacy::where('id', $order->orderBy_id)->get()->first();
            if(isset($pharmacy))
            $UserPharmacy = User::where('id', $pharmacy->user_id)->get()->first();
            else
            $UserPharmacy = null;

            $status = true;
            if($order->order_type == 'default_type') {
                return view('admin-views.order.order-details', compact('bagsOrder', 'status', 'order', 'delivery_men', 'pharmacy', 'UserPharmacy'));
            } else {
                return view('admin-views.pos.order.order-details', compact('status', 'order', 'pharmacy', 'UserPharmacy'));
            }
        }

        if ($order->order_type == 'default_type') {
            $status = false;
            return view('admin-views.order.order-details', compact('bagsOrder', 'status', 'order', 'delivery_men'));
        } else {
            return view('admin-views.pos.order.order-details', compact('status', 'order'));
        }
    }

    public function add_delivery_man($order_id, $delivery_man_id)
    {
        if ($delivery_man_id == 0) {
            return response()->json([], 401);
        }
        $order = Order::find($order_id);
        /*if($order->order_status == 'delivered' || $order->order_status == 'returned' || $order->order_status == 'failed' || $order->order_status == 'canceled' || $order->order_status == 'scheduled') {
            return response()->json(['status' => false], 200);
        }*/
        $order->delivery_man_id = $delivery_man_id;
        $order->delivery_type = 'self_delivery';
        $order->delivery_service_name = null;
        $order->third_party_delivery_tracking_id = null;
        $order->save();

        $fcm_token = $order->delivery_man->fcm_token;
        $value = Helpers::order_status_update_message('del_assign') . " ID: " . $order['id'];
        try {
            if ($value != null) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
            }
        } catch (\Exception $e) {
            Toastr::warning(\App\CPU\translate('Push notification failed for DeliveryMan!'));
        }

        return response()->json(['status' => true], 200);
    }


    public function add_pharmacy_man($order_id, $pharmacy_man_id)
    {
        if ($pharmacy_man_id == 0) {
            return response()->json([], 401);
        }

        try {
            $order = Order::find($order_id);
            $order->orderBy_id = $pharmacy_man_id;
            $order->save();
        } catch (\Exception $e) {
            Toastr::warning(\App\CPU\translate('failed for Pharmacy!'));
            return response()->json([], 401);
        }

        return response()->json(['status' => true], 200);
    }


    public function status(Request $request)
    {
        $order = Order::find($request->id);
        $fcm_token = $order->customer->cm_firebase_token;
        $value = Helpers::order_status_update_message($request->order_status);

        (isset($request->note_notify)) ? $note = " ( " . $request->note_notify . " ) " :  $note = "";

        try {
            if ($value) {
                $data = [
                    'title' => translate('Order'),
                    'description' => $value . '' . $note,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token, $data);
                Helpers::store_notif_to_db($order->customer->id, $data);
            }
        } catch (\Exception $e) {
        }

        try {
            $fcm_token_delivery_man = $order->delivery_man->fcm_token;
            if ($value != null) {
                $data = [
                    'title' => translate('order'),
                    'description' => $value,
                    'order_id' => $order['id'],
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($fcm_token_delivery_man, $data);
            }
        } catch (\Exception $e) {
        }

        $order->order_status = $request->order_status;

        if($request->order_status == "confirmed")
        {
            (isset($request->cost_center)) ? $order->cost_center=$request->cost_center : $order->cost_center=0;
            (isset($request->delivery_order)) ?  $order->Detection_number=$request->delivery_order :  $order->Detection_number=0;
            (isset($request->delivery_date)) ? $order->delivery_date =$request->delivery_date : $order->delivery_date =$order->delivery_date;
        }

        OrderManager::stock_update_on_order_status_change($order, $request->order_status);
        OrderManager::stock_update_on_bag_order_status_change($order, $request->order_status);
        $order->save();
        OrderManager::sendOrderToAlameenSystem($order->id, $request->order_status);

        $transaction = OrderTransaction::where(['order_id' => $order['id']])->first();
        if (isset($transaction) && $transaction['status'] == 'disburse') {
            return response()->json($request->order_status);
        }
        if ($request->order_status == 'delivered' && $order['seller_id'] != null) {
            OrderManager::wallet_manage_on_order_status_change($order, 'admin');
            OrderDetail::where('order_id', $order->id)->update(
                ['delivery_status' => 'delivered']
            );
            BagsOrdersDetails::where('order_id', $order->id)->update(
                ['delivery_status' => 'delivered']
            );
        }
        return response()->json($request->order_status);
    }

    public function payment_status(Request $request)
    {
        if ($request->ajax()) {
            $order = Order::find($request->id);
            $order->payment_status = $request->payment_status;
            $order->save();
            $data = $request->payment_status;
            return response()->json($data);
        }
    }

    public function generate_invoice($id)
    {
        $order = Order::with('seller')->with('shipping')->with('details')->where('id', $id)->first();
        $seller = Seller::find($order->details->first()->seller_id);
        $data["email"] = $order->customer != null ? $order->customer["email"] : \App\CPU\translate('email_not_found');
        $data["client_name"] = $order->customer != null ? $order->customer["f_name"] . ' ' . $order->customer["l_name"] : \App\CPU\translate('customer_not_found');
        $data["order"] = $order;

        $mpdf_view = \View::make('admin-views.order.invoice')->with('order', $order)->with('seller', $seller);
        Helpers::gen_mpdf($mpdf_view, 'order_invoice_', $order->id);
    }

    public function inhouse_order_filter()
    {
        if (session()->has('show_inhouse_orders') && session('show_inhouse_orders') == 1) {
            session()->put('show_inhouse_orders', 0);
        } else {
            session()->put('show_inhouse_orders', 1);
        }
        return back();
    }


    public function update_deliver_info(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->delivery_type = 'third_party_delivery';
        $order->delivery_service_name = $request->delivery_service_name;
        $order->third_party_delivery_tracking_id = $request->third_party_delivery_tracking_id;
        $order->delivery_man_id = null;
        $order->save();

        Toastr::success(\App\CPU\translate('updated_successfully!'));
        return back();
    }


    public function generate_excel(Request $request)
    {
        try {
            $orderDetails = OrderDetail::where('order_id', $request->order_id)->get();
            $orderItems = [];
            foreach ($orderDetails as $item) {
                $productDetails = json_decode($item->product_details);
              //get product name 
                $product_name=Product::where('id', $productDetails->id)->get();

                $orderItems[] = [
                    //'اسم المادة' =>  $productDetails->name,
                 	'اسم المادة' =>  $product_name[0]['name'],
                    'كمية المادة' => $item->qty,
                    'بونص المادة' => $item->total_qty,
                    'سعر المادة' => $item->price,
                    'تاريخ انتهاء الصلاحية' => $productDetails->expiry_date,
                ];
            }

            $orderBagsDetails = BagsOrdersDetails::where('order_id', $request->order_id)->get();
            foreach ($orderBagsDetails as $itemBag) {
                $bagDetails = json_decode($itemBag->bag_details);
                foreach ($bagDetails as $bagDetail) {
                    $orderItems[] = [
                        'اسم المادة' =>  $bagDetail->product_name,
                        'كمية المادة' => ($itemBag->bag_qty) * ($bagDetail->product_count),
                        'بونص المادة' => 0,
                        'سعر المادة' => $bagDetail->product_price,
                        'تاريخ انتهاء الصلاحية' => (isset($bagDetail->expiry_date)) ? $bagDetail->expiry_date : "0000-00-00",
                    ];
                }
            }
            $order = Order::where('id', $request->order_id)->firstOrFail();
            $fileName =$order->pharmacy_name;
            return (new FastExcel($orderItems))
                ->withoutHeaders()
                ->download($fileName.'.xlsx');
        } catch (\Exception $e) {
            Toastr::success(\App\CPU\translate('An_unexpected_error_occurred!'));
            return back();
        }
    }

  
    public function edit_order($id)
    {
        try {
            $order = Order::with('details', 'shipping', 'seller')->where(['id' => $id])->first();

            $productIdOrder = OrderDetail::where('order_id', '=', $id)->get(['product_id']);
            $products = Product::whereNotIn('id', $productIdOrder)->get(['id', 'name']);

            $bagIdOrder = BagsOrdersDetails::where('order_id', '=', $id)->get(['bag_id']);
            $bags = bag::whereNotIn('id', $bagIdOrder)->get(['id', 'bag_name']);
            $pharmacyName=$order->pharmacy_name ?? " ";
            if ($order->order_type == 'default_type') {
                return view('admin-views.order.order-editing', compact('order','bags', 'products','pharmacyName'));
            } else {
                return back();
            }
        } catch (Exception $e) {
            return back();
        }
    }


    public function delete_product_order(Request $request)
    {
        try {
            if ($request->ajax()) {
                $product = OrderDetail::where('order_id', '=', $request->order_id)
                    ->where('product_id', '=', $request->product_id)->get()->first();
                OrderManager::stock_update_on_order_delete_change($product, $request->order_id);
                $product->delete();
            }
            $product_name = Product::where('id', '=', $request->product_id)->get()->first();
            if (isset($product_name))
                OrderServices::sendNotificationDeleteOrder($product_name->name, $request->order_id);
            $data = 1;
            return response()->json($data);
        } catch (\Exception $e) {
            $data = 0;
            return response()->json($data);
        }
    }


    public function product_edit_order(Request $request, $id)
    {
        $orderDetail = OrderDetail::where('order_id', '=', $id)
            ->where('product_id', '=', $request->product_id)->get()->first();
        return response()->json([
            'data' => $orderDetail
        ]);
    }


    public function update_order(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'qty_offer' => 'required|numeric|min:0|max:1000',
                'price' => 'required|numeric|min:0|max:10000000',
                'qty' => 'required|numeric|min:0|max:1000',
                'order_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                Toastr::error('Faild updated!');
            }
            $orderProductDetails = OrderDetail::where('order_id', '=', $request->order_id)
                ->where('product_id', '=', $request->product_id)->get()->first();

            OrderManager::stock_update_on_order_edit_change($orderProductDetails, $request->order_id, $request->qty,$request->qty_offer,$request->price);

            $product = Product::where('id', '=', $request->product_id)->get()->first();
            if (isset($product))
                OrderServices::sendNotificationUpdateOrder($product->name, $request->order_id);

            Toastr::success('Quantity updated successfully!');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Faild updated!');
            return back();
        }
    }

    public function generate_excel_all(Request $request)
    {
        $from = session('from_date');
        $to = session('to_date');

      	if(isset($request["from"]) && $request["to"]){
            $from = $request["from"];
       		$to =$request["to"];
        }
      
        $orderDetails = Order::whereBetween('created_at', [$from, $to])->get();
  
        $storage = [];

        foreach ($orderDetails as $item) {
            if ($item->customer_type == "pharmacist") {
                $user = User::where('id', $item->customer_id)->get()->first();
                if(isset($user))
                $pharmacy = Pharmacy::where('user_id', $item->customer_id)->get()->first();
            } else {
                $pharmacy = Pharmacy::where('id', $item->orderBy_id)->get()->first();
                if(isset($pharmacy))
                $user = User::where('id', $pharmacy->user_id)->get()->first();
            }
            $order_status = translate($item->order_status);
            $order_paid = translate($item->payment_status);
            $storage[] = [
                'رقم الطلبية' => $item->id,
                'اسم الزبون' => $pharmacy->name ?? "",
                'السعر الاجمالي' => $item->order_amount,
                'المنطقة' => $pharmacy->region ?? "",
                'العنوان' => $pharmacy->Address ?? "",
                'رقم الهاتف' => $user->phone ?? "",
                'حالة الطلبية' => $order_status,
                'حالة الدفع' => $order_paid,
                'تاريخ الطلبية' => $item->created_at,
                'تاريخ التسليم' => $item->delivery_date,
                'رقم البطاقة' => $pharmacy->card_number ?? "",
                'رقم الحساب' => $user->pharmacy_id ?? "",
                'رقم الكشف' => $item->Detection_number,
            ];
        }

        $xlsx = ".xlsx";
        $result = 'الطلبيات' . now() . '' . $xlsx;
        return (new FastExcel($storage))->download($result);
    }

    public function show_order_details($order_id)
    {
        $order = Order::where('id', '=', $order_id)->get()->first();
        if ($order->orderBy_id != 0) {
            $cus_id = $order->orderBy_id;         //pharamcy
            $details = Pharmacy::where('id', '=', $cus_id)->get()->first();
        } else {
            $cus_id = $order->customer_id;       //user
            $details = Pharmacy::where('user_id', '=', $cus_id)->get()->first();
        }

        return response()->json([
            'data' => $details
        ]);
    }

    public function insert_order(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'product_id' => 'required',
                'order_idp' => 'required',
                'qty_product' => 'required',
            ]);
            if ($validator->fails()) {
                Toastr::error('Faild Insert!');
            }
            DB::transaction(function () use ($request) {
                $product = Product::where(['id' => $request->product_id])->first();
                $order = Order::where(['id' => $request->order_idp])->first();
                $total_qty = OrderManager::featured_offer_calculation($product->q_featured_offer, $product->featured_offer, $request->qty_product);
                ($total_qty != 0) ? $offerType = 'featured' : $offerType = 'no offer';
                if ($total_qty == 0) {
                    $total_qty = OrderManager::normal_offer_calculation($product->q_normal_offer, $product->normal_offer, $request->qty_product);
                    ($total_qty != 0) ? $offerType = 'normal' : $offerType = 'no offer';
                }
                $pure_price = 0;
                $or_d = [
                    'order_id' => $request->order_idp,
                    'product_id' => $request->product_id,
                    'seller_id' => 1,
                    'product_details' => $product,
                    'qty' => $request->qty_product,
                    'total_qty' => $total_qty,
                    'offerType' => $offerType,
                    'price' => $product->unit_price,
                    'tax' => 0,
                    'discount' => 0,
                    'discount_type' => 'discount_on_product',
                    'variant' => "",
                    'variation' => "",
                    'delivery_status' => 'pending',
                    'shipping_method_id' => null,
                    'pure_price' => $pure_price,
                    'payment_status' => $order->payment_status,
                    'brand_id' => $product->brand_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                DB::table('order_details')->insert($or_d);
                Product::where(['id' => $product['id']])->update([
                    'current_stock' => $product['current_stock'] - $request->qty_product - $total_qty
                ]);
                $order->order_amount = $order->order_amount + ($request->qty_product * $product->unit_price);
                $order->save();
                OrderServices::sendNotificationInsertOrder($product->name, $order['id']);
            });
            Toastr::success('Insert product successfully!');
            return back();
        } catch (\Exception $e) {
            Toastr::error('Faild Insert!');
            return back();
        }
    }


    public function insert_order_bag(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'bag_id' => 'required',
                'order_idb' => 'required',
                'bag_qty' => 'required',
            ]);
            if ($validator->fails()) {
                Toastr::error('Faild Insert!');
            }
            DB::transaction(function () use ($request) {
                $bag = Bag::where(['id' => $request->bag_id])->first();
                $order = Order::where(['id' => $request->order_idb])->first();
                $bagProducts=BagServices::getProductsBybag($bag->id);
                $or_d = [
                    'order_id' => $order->id,
                    'bag_id' => $bag->id,
                    'seller_id' => 1,
                    'bag_details' => json_encode($bagProducts, true),
                    'bag_qty' => $request->bag_qty,
                    'bag_price' => $bag->total_price_offer,
                    'bag_tax' => 0,
                    'bag_discount' => 0,
                    'delivery_status' => 'pending',
                    'payment_status' => 'unpaid',
                    'is_stock_decreased' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                DB::table('bags_orders_details')->insert($or_d);
                $order->order_amount = $order->order_amount + ($request->bag_qty * $bag->total_price_offer);
                $order->save();
                OrderServices::sendNotificationInsertOrderBag($bag->bag_name, $order['id']);
            });
            Toastr::success('Insert bag successfully!');
            return back();
        } catch (\Exception $e) {
            dd($e);
            Toastr::error('Faild Insert!');
            return back();
        }
    }
}
