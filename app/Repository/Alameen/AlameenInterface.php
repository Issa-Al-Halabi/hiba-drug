<?php

namespace App\Repository\Alameen;
use App\Pharmacy;

interface AlameenInterface{
    public function getAllData();
    public function view($orderId);
    public function storeOrUpdate($orderId = null,$data);
    public function updateStatus($orderId,$Orderstatus);
}
