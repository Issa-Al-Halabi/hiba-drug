<?php

namespace App\Repository\User;
use App\Pharmacy;

interface UserInterface{
    public function getAllData();
    public function storeOrUpdate($id = null,$data);
    public function view($id);
    public function searchAccountNumber($id);
    public function delete($id);
}
