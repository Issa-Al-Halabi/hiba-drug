<?php

namespace App\Services;

use App\Repository\Alameen\AlameenRepository;
use App\Repository\Alameen\AlameenInterface;
use App\Model\BagsOrdersDetails;
use App\Model\OrderDetail;
use App\Model\BagProduct;
use App\Model\Product;
use App\Model\Order;
use App\Pharmacy;
use App\User;
use Exception;

class AlameenSystemServices
{
    protected AlameenInterface $AlameenObject;
    protected $statusStore;

    public function __construct()
    {
        $this->AlameenObject = new AlameenRepository;
    }

    public function storeOrder($orderId)
    {
        try {
            $this->AlameenObject->storeOrUpdate(null, $this->getOrderDetails($orderId));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function getOrderDetails($orderId)
    {
        try {
            $pharamcyDetails = $this->getOrderPharmacyDetails($orderId);
            $orderCredential = $this->getOrderCredential($orderId);
            $data = [
                'pharmacyId' => $pharamcyDetails['id'],
                'orderId' => $orderId,
                'pharmacyName' => $pharamcyDetails['name'],
                'productDetails' => $this->getOrderProducts($orderId),
                'orderStatus' => $orderCredential['status'],
                'deliveryDate' => $orderCredential['deliveryDate'],
                'costCenter' => $orderCredential['costCenter'],
                'detectionNumber' => $orderCredential['detectionNumber'],
                'customerType' => $orderCredential['customerType']
            ];
            return $data;
        } catch (Exception $e) {
        }
    }

    public function getOrderProducts($orderId)
    {
        try {
            $orderProductDetailsList = [];
            $orderProductsDetails = OrderDetail::where('order_id', '=', $orderId)->get();
            $orderBagsDetails = BagsOrdersDetails::where('order_id', '=', $orderId)->get();

            foreach ($orderProductsDetails as $orderProductDetails) {
                $product = Product::where('id', $orderProductDetails->product_id)->get()->first();
                $productDeatils = [
                    'product_id' => $product->num_id,
                    'qty' => $orderProductDetails->qty,
                    'price' => $orderProductDetails->price,
                    'q_gift' => $orderProductDetails->total_qty,
                    'store_id' => $product->store_id,
                ];
                array_push($orderProductDetailsList, $productDeatils);
            }

            foreach ($orderBagsDetails as $orderBagDetails) {
                $bagProducts = BagProduct::where(['bag_id' => $orderBagDetails->bag_id])->get();
                foreach($bagProducts as $bagProduct)
                {
                    $product = Product::where('id', $bagProduct->product_id)->get()->first();
                    $bagProductDeatils = [
                        'product_id' => $product->num_id,
                        'qty' => ($bagProduct->is_gift == 0) ? $bagProduct->product_count * $orderBagDetails->bag_qty : 0,
                        'price' => $bagProduct->product_price,
                        'q_gift' => ($bagProduct->is_gift == 1) ? $bagProduct->product_count * $orderBagDetails->bag_qty : 0,
                        'store_id' => $product->store_id,
                    ];
                    array_push($orderProductDetailsList, $bagProductDeatils);
                }
            }

            return $orderProductDetailsList;
        } catch (Exception $e) {
        }
    }

    public function getOrderPharmacyDetails($orderId)
    {
        try {
            $order = Order::where('id', '=', $orderId)->get()->first();
            if ($order->customer_type == "salesman") {
                $pharmacy = Pharmacy::where('id', '=', $order->orderBy_id)->get()->first();
                $pharmacyId = $pharmacy->customer->pharmacy_id;
                $pharmacyName = $pharmacy->name;
            } else {
                $customer = User::where('id', '=', $order->customer_id)->get()->first();
                $pharmacyId = $customer->pharmacy_id;
                $pharmacyName = $customer->pharmacy->name;
            }
            return  [
                'id' => $pharmacyId,
                'name' => $pharmacyName
            ];
        } catch (Exception $e) {

        }
    }

    public function getOrderCredential($orderId)
    {
        try {
            $order = Order::where('id', '=', $orderId)->get()->first();
            return  [
                'status' => $order->order_status,
                'deliveryDate' => $order->delivery_date,
                'costCenter' => $order->cost_center,
                'detectionNumber' => $order->Detection_number,
                'customerType' => $order->customer_type
            ];
        } catch (Exception $e) {

        }
    }

}
