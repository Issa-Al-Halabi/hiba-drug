<?php

namespace App\Services;

use App\CPU\Helpers;
use App\Model\Order;
use function App\CPU\translate;

class OrderServices
{

    public static function sendNotificationDeleteOrder($productName, $orderId)
    {
        $order = Order::where('id', '=', $orderId)->get()->first();
        try {
            if (true) {
                $data = [
                    'title' => translate('Order'),
                    'description' => OrderServices::deleteProductOrderMessage($productName, $orderId),
                    'order_id' => $orderId,
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($order->customer->cm_firebase_token, $data);
                Helpers::store_notif_to_db($order->customer->id, $data);
            }
        } catch (\Exception $e) {
        }
    }


    public static function sendNotificationUpdateOrder($productName, $orderId)
    {
        $order = Order::where('id', '=', $orderId)->get()->first();
        try {
            if (true) {
                $data = [
                    'title' => translate('Order'),
                    'description' => OrderServices::updateProductOrderMessage($productName, $orderId),
                    'order_id' => $orderId,
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($order->customer->cm_firebase_token, $data);
                Helpers::store_notif_to_db($order->customer->id, $data);
            }
        } catch (\Exception $e) {
        }
    }


    public static function sendNotificationInsertOrder($productName, $orderId)
    {
        $order = Order::where('id', '=', $orderId)->get()->first();
        try {
            if (true) {
                $data = [
                    'title' => translate('Order'),
                    'description' => OrderServices::insertProductOrderMessage($productName, $orderId),
                    'order_id' => $orderId,
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($order->customer->cm_firebase_token, $data);
                Helpers::store_notif_to_db($order->customer->id, $data);
            }
        } catch (\Exception $e) {
        }
    }

    public static function sendNotificationInsertOrderBag($BagName, $orderId)
    {
        $order = Order::where('id', '=', $orderId)->get()->first();
        try {
            if (true) {
                $data = [
                    'title' => translate('Order'),
                    'description' => OrderServices::insertBagOrderMessage($BagName, $orderId),
                    'order_id' => $orderId,
                    'image' => '',
                ];
                Helpers::send_push_notif_to_device($order->customer->cm_firebase_token, $data);
                Helpers::store_notif_to_db($order->customer->id, $data);
            }
        } catch (\Exception $e) {
        }
    }


    public static function deleteProductOrderMessage($productName, $orderId)
    {
        $message = "تم إزالة المنتج (" . $productName . ") رقم الطلبية (" . $orderId . ")";
        return $message;
    }


    public static function insertProductOrderMessage($productName, $orderId)
    {
        $message = "تم إضافة المنتج (" . $productName . ") رقم الطلبية (" . $orderId . ")";
        return $message;
    }

    public static function insertBagOrderMessage($BagName, $orderId)
    {
        $message = "تم إضافة سلة الأدوية  (" . $BagName . ") رقم الطلبية (" . $orderId . ")";
        return $message;
    }


    public static function updateProductOrderMessage($productName, $orderId)
    {
        $message = "تم التعديل على كمية المنتج (" . $productName . ") رقم الطلبية (" . $orderId . ")";
        return $message;
    }
}
