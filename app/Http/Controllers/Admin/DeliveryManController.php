<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Http\Controllers\Controller;
use App\Model\DeliveryMan;
use App\Model\Admin;
use App\Model\DeliveryReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use function App\CPU\translate;
use App\Model\Order;
use Brian2694\Toastr\Facades\Toastr;
use Exception;

class DeliveryManController extends Controller
{

    public function index()
    {
        return view('admin-views.delivery-man.index');
    }

    public function list(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $delivery_men = DeliveryMan::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                        ->orWhere('l_name', 'like', "%{$value}%")
                        ->orWhere('phone', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $delivery_men = new DeliveryMan();
        }

        $delivery_men = $delivery_men->latest()->where(['seller_id' => 0])->paginate(25)->appends($query_param);
        return view('admin-views.delivery-man.list', compact('delivery_men', 'search'));
    }

    public function search(Request $request)
    {
        $key = explode(' ', $request['search']);
        $delivery_men = DeliveryMan::where(['seller_id' => 0])->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%")
                    ->orWhere('email', 'like', "%{$value}%")
                    ->orWhere('phone', 'like', "%{$value}%")
                    ->orWhere('identity_number', 'like', "%{$value}%");
            }
        })->get();
        return response()->json([
            'view' => view('admin-views.delivery-man.partials._table', compact('delivery_men'))->render()
        ]);
    }


    public function preview($id)
    {
        $delivery = DeliveryMan::with(['reviews'])->where(['id' => $id])->first();
        $reviews = DeliveryReview::where('delivery_id','=',$id)->paginate(6);

        return view('admin-views.delivery-man.view', compact('delivery', 'reviews'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'email' => 'required|unique:delivery_men',
            'phone' => 'required|unique:delivery_men',
        ], [
            'f_name.required' => 'First name is required!'
        ]);


        $id_img_names = [];
        if (!empty($request->file('identity_image'))) {
            foreach ($request->identity_image as $img) {
                array_push($id_img_names, ImageManager::upload('delivery-man/', 'png', $img));
            }
            $identity_image = json_encode($id_img_names);
        } else {
            $identity_image = json_encode([]);
        }

        $dm = new DeliveryMan();
        $dm->seller_id = 0;
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->phone = $request->phone;
        $dm->identity_number = $request->identity_number;
        $dm->identity_type = $request->identity_type;
        $dm->identity_image = $identity_image;
        $dm->image = ImageManager::upload('delivery-man/', 'png', $request->file('image'));
        $dm->password = bcrypt($request->password);
        $dm->save();

        Toastr::success('Delivery-man added successfully!');
        return redirect('admin/delivery-man/list');
    }

    public function edit($id)
    {
        $delivery_man = DeliveryMan::find($id);
        return view('admin-views.delivery-man.edit', compact('delivery_man'));
    }

    public function status(Request $request)
    {
        $delivery_man = DeliveryMan::find($request->id);
        $delivery_man->is_active = $request->status;
        $delivery_man->save();
        return response()->json([], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'f_name' => 'required',
            'email' => 'required|email|unique:delivery_men,email,' . $id,
            'phone' => 'required|unique:delivery_men,phone,' . $id,
        ], [
            'f_name.required' => 'First name is required!'
        ]);

        $delivery_man = DeliveryMan::where(['id' => $id, 'seller_id' => 0])->first();
        if (isset($delivery_man) && $request['email'] != $delivery_man['email']) {
            $request->validate([
                'email' => 'required|unique:delivery_men',
            ]);
        }

        if (!empty($request->file('identity_image'))) {
            foreach (json_decode($delivery_man['identity_image'], true) as $img) {
                if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                    Storage::disk('public')->delete('delivery-man/' . $img);
                }
            }
            $img_keeper = [];
            foreach ($request->identity_image as $img) {
                array_push($img_keeper, ImageManager::upload('delivery-man/', 'png', $img));
            }
            $identity_image = json_encode($img_keeper);
        } else {
            $identity_image = $delivery_man['identity_image'];
        }
        $delivery_man->seller_id = 0;
        $delivery_man->f_name = $request->f_name;
        $delivery_man->l_name = $request->l_name;
        $delivery_man->email = $request->email;
        $delivery_man->phone = $request->phone;
        $delivery_man->identity_number = $request->identity_number;
        $delivery_man->identity_type = $request->identity_type;
        $delivery_man->identity_image = $identity_image;
        $delivery_man->image = $request->has('image') ? ImageManager::update('delivery-man/', $delivery_man->image, 'png', $request->file('image')) : $delivery_man->image;
        $delivery_man->password = strlen($request->password) > 1 ? bcrypt($request->password) : $delivery_man['password'];
        $delivery_man->save();

        Toastr::success('Delivery-man updated successfully!');
        return redirect('admin/delivery-man/list');
    }

    public function delete(Request $request)
    {
        $delivery_man = DeliveryMan::find($request->id);
        if (Storage::disk('public')->exists('delivery-man/' . $delivery_man['image'])) {
            Storage::disk('public')->delete('delivery-man/' . $delivery_man['image']);
        }

        foreach (json_decode($delivery_man['identity_image'], true) as $img) {
            if (Storage::disk('public')->exists('delivery-man/' . $img)) {
                Storage::disk('public')->delete('delivery-man/' . $img);
            }
        }
        DeliveryReview::where('delivery_id','=',$delivery_man->id)->delete();
        $delivery_man->delete();
        Toastr::success(translate('Delivery-man removed!'));
        return back();
    }

    public function scheduling_index(Request $request, $status)
    {

        $query_param = [];
        $search = $request['search'];

        if ($status != 'all') {
            $orders = Order::with(['customer', 'delivery_man'])->whereIn('order_status', ['processing', 'confirmed']);
        } else {
            $orders = Order::with(['customer', 'delivery_man']);
        }

        Order::where(['checked' => 0])->update(['checked' => 1]);

        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $orders = $orders->where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('id', 'like', "%{$value}%")
                        ->orWhere('customer_type', 'like', "%{$value}%")
                        ->orWhere('delivery_date', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        }


        $orders = $orders->where('order_type', 'default_type')->orderBy('id', 'desc')->paginate(Helpers::pagination_limit())->appends($query_param);

        return view('admin-views.delivery-trip.index', compact('orders', 'search'));
    }


    public function scheduling_edit($id)
    {
        $order = Order::find($id);
        return view('admin-views.delivery-trip.edit', compact('order'));
    }


    public function scheduling_update(Request $request, $id)
    {

        $request->validate([
            'delivery_date' => 'required|date',
            'Detection_number' => 'required|numeric',

            'order_number' => 'required|numeric',
        ], [
            'delivery_date.required' => 'Delivery date is required!',
            'Detection_number.required' => 'Detection number is required!',

            'order_number.required' => 'Order number is required!',
        ]);

        $order = Order::findOrfail($id);
        $order->delivery_date = $request->delivery_date;
        $order->Detection_number = $request->Detection_number;
        $order->order_number = $request->order_number;
        $order->save();

        Toastr::success('updated successfully!');
        return redirect()->back();
    }

    public function changeScheduling(Request $request)
    {

        $order = order::find($request->id);

        if ($order->scheduling == true)
            $order->scheduling = false;
        else
            $order->scheduling = true;
        $order->save();

        Toastr::success('Scheduling status updated successfully!');
        return back();
    }


    public function reviewList(Request $request)
    {
        $query_param = [];
        $search = $request['search'];
        if ($request->has('search')) {
            $key = explode(' ', $request['search']);
            $lists = DeliveryMan::where(function ($q) use ($key) {
                foreach ($key as $value) {
                    $q->orWhere('f_name', 'like', "%{$value}%")
                    ->orWhere('l_name', 'like', "%{$value}%");
                }
            });
            $query_param = ['search' => $request['search']];
        } else {
            $lists = new DeliveryMan();
        }
        $lists = $lists->latest()->paginate(Helpers::pagination_limit())->appends($query_param);
        return view('admin-views.delivery-man.reviewList', compact('lists', 'search'));
    }


    public function store_review(Request $request)
    {
        $request->validate([
            'rating' => 'required',
            'comment' => 'required',
            'delivery_id'=> 'required',
        ], [
            'rating.required' => 'Rating is required!',
            'comment.required' => 'Comment is required!',
            'delivery_id.required' => 'Delivery ID is required!',
        ]);

        try {
            $emp=Admin::where('id','=',auth('admin')->id())->get()->first();
            $deliveryReview = new DeliveryReview;
            $deliveryReview->delivery_id = $request->delivery_id;
            $deliveryReview->delivery_comment = $request->comment;
            $deliveryReview->delivery_rating = $request->rating;
            $deliveryReview->emp_name = $emp->name;
            $deliveryReview->save();
            Toastr::success('Review added successfully!');
            return back();
        } catch (Exception $e) {
            return back();
            Toastr::success('Review added Failure!');
        }
    }


}

