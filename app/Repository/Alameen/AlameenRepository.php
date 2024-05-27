<?php

namespace App\Repository\Alameen;
use App\Model\OrderAlameen;
use function is;
use function is_null;

class AlameenRepository implements AlameenInterface{

    public function getAllData(){
        return OrderAlameen::latest()->get();
    }

    public function storeOrUpdate($orderId = null,$data){
        if(is_null($orderId)){
            $orderAlameen = new OrderAlameen();
            $orderAlameen->pharmacy_id = $data['pharmacyId'];
            $orderAlameen->order_id=$data['orderId'];
            $orderAlameen->pharmacy_name = $data['pharmacyName'];
            $orderAlameen->product_details = json_encode($data['productDetails']);
            $orderAlameen->status = $data['orderStatus'];
            $orderAlameen->cost_center = $data['costCenter'];
            $orderAlameen->Detection_number = $data['detectionNumber'];
            $orderAlameen->delivery_date = $data['deliveryDate'];
            $orderAlameen->customer_type = $data['customerType'];
            return $orderAlameen->save();
        }else{
            $orderAlameen = OrderAlameen::where('order_id','=',$orderId)->get()->first();
            if($orderAlameen==null)
            $orderAlameen = new OrderAlameen();
            $orderAlameen->pharmacy_id = $data['pharmacyId'];
            $orderAlameen->order_id=$data['orderId'];
            $orderAlameen->pharmacy_name = $data['pharmacyName'];
            $orderAlameen->product_details = json_encode($data['productDetails']);
            $orderAlameen->status = $data['orderStatus'];
            $orderAlameen->cost_center = $data['costCenter'];
            $orderAlameen->Detection_number = $data['detectionNumber'];
            $orderAlameen->delivery_date = $data['deliveryDate'];
            $orderAlameen->customer_type = $data['customerType'];
            return $orderAlameen->save();
        }
    }

    public function view($orderId){
        return OrderAlameen::where('order_id','=',$orderId)->get()->first();
    }

    public function updateStatus($orderId,$Orderstatus)
    {
        $orderAlameen = OrderAlameen::where('order_id','=',$orderId)->get()->first();
        $orderAlameen->status=$Orderstatus;
        $orderAlameen->save();
    }

}
