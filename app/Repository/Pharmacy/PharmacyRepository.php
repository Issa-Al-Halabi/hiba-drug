<?php

namespace App\Repository\Pharmacy;
use App\Pharmacy;
use function is;
use function is_null;

class PharmacyRepository implements PharmacyInterface{
    public function getAllData(){
        return Pharmacy::latest()->get();
    }

    public function storeOrUpdate($id = null,$data){
        if(is_null($id)){
            $pharmacy = new Pharmacy();
            $pharmacy->name = $data['name'];
            $pharmacy->lat = $data['lat'];
            $pharmacy->lan = $data['lan'];
            $pharmacy->city = $data['city'];
            $pharmacy->region = $data['region'];
            $pharmacy->user_id = $id;
            $pharmacy->user_type_id = $data['user_type_id'];
            $pharmacy->from = $data['from'];
            $pharmacy->to = $data['to'];
            $pharmacy->Address = $data['Address'];
            $pharmacy->land_number = $data['land_number'];
            $pharmacy->card_number = $data['card_number'];
            return $pharmacy->save();
        }else{
            $pharmacy = Pharmacy::where('user_id','=',$id)->get()->first();
            if($pharmacy==null)
            $pharmacy = new Pharmacy();

            $pharmacy->name = $data['name'];
            $pharmacy->lat = $data['lat'];
            $pharmacy->lan = $data['lan'];
            $pharmacy->city = $data['city'];
            $pharmacy->region = $data['region'];
            $pharmacy->user_id = $id;
            $pharmacy->user_type_id = $data['user_type_id'];
            $pharmacy->from = $data['from'];
            $pharmacy->to = $data['to'];
            $pharmacy->Address = $data['Address'];
            $pharmacy->land_number = $data['land_number'];
            $pharmacy->card_number = $data['card_number'];
            return $pharmacy->save();
        }
    }

    public function view($id){
        return Pharmacy::find($id);
    }
    public function delete($id){
        return Pharmacy::find($id)->delete();
    }
}
