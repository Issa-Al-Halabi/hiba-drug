<?php

namespace App\Repository\Pharmacy;
use App\Pharmacy;

interface PharmacyInterface{
    public function getAllData();
    public function storeOrUpdate($id = null,$data);
    public function view($id);
    public function delete($id);
}
