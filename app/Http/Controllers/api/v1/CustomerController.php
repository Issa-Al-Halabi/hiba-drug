<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\CustomerManager;
use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\Order;
use App\Model\Product;
use App\Model\OrderDetail;
use App\Model\Visitors;
use App\Model\ShippingAddress;
use App\Model\BagsOrdersDetails;
use App\Model\Bag;
use App\Model\SupportTicket;
use App\Model\SupportTicketConv;
use App\Model\Wishlist;
use App\User;
use App\Pharmacy;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use Carbon\Carbon;

class CustomerController extends Controller
{
    
    public function info(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    public function create_support_ticket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required',
            'type' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $request['customer_id'] = $request->user()->id;
        $request['priority'] = 'low';
        $request['status'] = 'pending';

        try {
            CustomerManager::create_support_ticket($request);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    'code' => 'failed',
                    'message' => 'Something went wrong',
                ],
            ], 422);
        }
        return response()->json(['message' => 'Support ticket created successfully.'], 200);
    }

    public function reply_support_ticket(Request $request, $ticket_id)
    {
        $support = new SupportTicketConv();
        $support->support_ticket_id = $ticket_id;
        $support->admin_id = 1;
        $support->customer_message = $request['message'];
        $support->save();
        return response()->json(['message' => 'Support ticket reply sent.'], 200);
    }

    public function get_support_tickets(Request $request)
    {
        $tickets = SupportTicket::where('customer_id', $request->user()->id)->get();
        foreach ($tickets as $ticket) {
            if ($ticket['status'] == 'open')
                $ticket['status'] = 1;   //open
            elseif ($ticket['status'] == 'close')
                $ticket['status'] = 0;   //close
            else
                $ticket['status'] = 2;  //pending
        }
        return response()->json($tickets, 200);
    }

    public function get_support_ticket_conv($ticket_id)
    {
        return response()->json(SupportTicketConv::where('support_ticket_id', $ticket_id)->get(), 200);
    }

    public function add_to_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $product = Product::find($request->product_id);
        if (isset($product)) {
            $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();

            if (empty($wishlist)) {
                $wishlist = new Wishlist;
                $wishlist->customer_id = $request->user()->id;
                $wishlist->product_id = $request->product_id;
                $wishlist->save();
                return response()->json(['message' => translate('successfully added!')], 200);
            }
            return response()->json(['message' => translate('Already in your wishlist')], 409);
        } else {
            return response()->json(['message' => translate('Product Id not found')], 409);
        }
    }

    public function remove_from_wishlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $wishlist = Wishlist::where('customer_id', $request->user()->id)->where('product_id', $request->product_id)->first();

        if (!empty($wishlist)) {
            Wishlist::where(['customer_id' => $request->user()->id, 'product_id' => $request->product_id])->delete();
            return response()->json(['message' => translate('successfully removed!')], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

    public function wish_list(Request $request)
    {
        return response()->json(Wishlist::whereHas('product')->with(['product'])->where('customer_id', $request->user()->id)->get(), 200);
    }

    public function address_list(Request $request)
    {
        return response()->json(ShippingAddress::where('customer_id', $request->user()->id)->get(), 200);
    }

    public function add_new_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contact_person_name' => 'required',
            'address_type' => 'required',
            'address' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'phone' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
            'is_billing' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $address = [
            'customer_id' => $request->user()->id,
            'contact_person_name' => $request->contact_person_name,
            'address_type' => $request->address_type,
            'address' => $request->address,
            'city' => $request->city,
            'zip' => $request->zip,
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_billing' => $request->is_billing,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('shipping_addresses')->insert($address);
        return response()->json(['message' => translate('successfully added!')], 200);
    }

    public function delete_address(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (DB::table('shipping_addresses')->where(['id' => $request['address_id'], 'customer_id' => $request->user()->id])->first()) {
            DB::table('shipping_addresses')->where(['id' => $request['address_id'], 'customer_id' => $request->user()->id])->delete();
            return response()->json(['message' => 'successfully removed!'], 200);
        }
        return response()->json(['message' => translate('No such data found!')], 404);
    }

     public function get_order_list(Request $request)
    {
        try {
            if ($request->has('filter_last_date')) {
                $last_date_filter = $request->filter_last_date;
                $now = Carbon::now();
                $start_date = $now->copy()->subDays($last_date_filter);
                if ($request->has('order_id'))
                    $orders = Order::with('delivery_man')->where(['customer_id' => $request->user()->id])->where(['id' => $request->order_id])->whereBetween('created_at', [$start_date, $now])->get();
                else
                    $orders = Order::with('delivery_man')->where(['customer_id' => $request->user()->id])->whereBetween('created_at', [$start_date, $now])->get();
            } else {
                if ($request->has('order_id'))
                    $orders = Order::with('delivery_man')->where(['customer_id' => $request->user()->id])->where(['id' => $request->order_id])->get();
                else
                    $orders = Order::with('delivery_man')->where(['customer_id' => $request->user()->id])->get();
            }
            $orders->map(function ($data) {
                $data['shipping_address_data'] = json_decode($data['shipping_address_data']);
                $data['billing_address_data'] = json_decode($data['billing_address_data']);
                return $data;
            });
            foreach ($orders as $order) {
                $order['order_status'] = $order['order_status'];
            }
            return response()->json($orders, 200);
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = OrderDetail::where(['order_id' => $request['order_id']])->get();
        $bagDetails = BagsOrdersDetails::where(['order_id' => $request['order_id']])->get();
        foreach ($bagDetails as $bagDetail) {
            $bagDetail['order_id'] = (int)$bagDetail['order_id'];
            $bagDetail['bag_id'] = (int)$bagDetail['bag_id'];

            $bag = Bag::where('id', '=', (int)$bagDetail['bag_id'])->get()->first();
            if (isset($bag)) {
                $bagDetail['bag_name'] = $bag->bag_name;
                $bagDetail['bag_image'] = $bag->bag_image;
            }
        }

        $details->map(function ($query) {
            $query['variation'] = json_decode($query['variation'], true);
            $query['product_details'] = Helpers::product_data_formatting(json_decode($query['product_details'], true));
            return $query;
        });

        $bagDetails->map(function ($query) {
            $query['bag_details'] = json_decode($query['bag_details'], true);
            return $query;
        });
        return response()->json(['products' => $details, 'bags' => $bagDetails], 200);
    }

    //Get list of orders for sellers of the pharmacy
    public function orderListSellers(Request $request)
    {
        try {
            $pharmacy = Pharmacy::where('user_id', '=', $request->user()->id)->get()->first();
            $orders = Order::with('delivery_man')->where(['orderBy_id' => $pharmacy->id])->get();
            $orders->map(function ($data) {
                $data['shipping_address_data'] = json_decode($data['shipping_address_data']);
                $data['billing_address_data'] = json_decode($data['billing_address_data']);
                return $data;
            });
            foreach ($orders as $order) {
                $user = User::where('id', '=', $order->customer_id)->get()->first();
                $order['seller_name'] = $user->name;
            }
            return response()->json($orders, 200);
        } catch (Exception $e) {
            return response()->json([], 404);
        }
    }

    public function update_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'phone' => 'required',
        ], [
            'f_name.required' => translate('First name is required!'),
            'l_name.required' => translate('Last name is required!'),
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->has('image')) {
            $imageName = ImageManager::update('profile/', $request->user()->image, 'png', $request->file('image'));
        } else {
            $imageName = $request->user()->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $request->user()->password;
        }

        $userDetails = [
            'f_name' => $request->f_name,
            'l_name' => $request->l_name,
            'phone' => $request->phone,
            'image' => $imageName,
            'password' => $pass,
            'updated_at' => now(),
        ];

        User::where(['id' => $request->user()->id])->update($userDetails);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function update_cm_firebase_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cm_firebase_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        DB::table('users')->where('id', $request->user()->id)->update([
            'cm_firebase_token' => $request['cm_firebase_token'],
        ]);

        return response()->json(['message' => translate('successfully updated!')], 200);
    }

    public function visitors(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'serial_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $visitor = Visitors::where('serial_number', '=', $request->serial_number)->get()->first();
        if (isset($visitor)) {
            return response()->json(['message' => 'The user is already visitor!!!'], 200);
        } else {
            $visitorNew = new Visitors();
            $visitorNew->serial_number = $request->serial_number;
            $visitorNew->save();
            return response()->json(['message' => 'successfully add!'], 200);
        }
    }
}
