<?php

namespace App\Repository\User;
use App\User;
use function is;
use function is_null;

class UserRepository implements UserInterface{
    public function getAllData(){
        return User::latest()->get();
    }

    public function storeOrUpdate($id = null,$data){
        if(is_null($id)){
            $user = new User();
            $user->name = $data['name'];
            $user->f_name = $data['f_name'];
            $user->l_name = $data['l_name'];
            $user->phone = $data['phone'];
            $user->pharmacy_id = $data['pharmacy_id'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->user_type = $data['user_type'];
            $user->area_id = $data['area_id'];
            $user->is_phone_verified = 1;
            $user->is_active = 1;
            $user->street_address = $data['street_address'];
            $user->country = $data['country'];
            $user->city = $data['city'];
            $user->save();
            return $user;
        }else{
            $user =User::where('pharmacy_id','=',$id)->get()->first();
            $user->name = $data['name'];
            $user->f_name = $data['f_name'];
            $user->l_name = $data['l_name'];
            $user->phone = $data['phone'];
            $user->pharmacy_id = $data['pharmacy_id'];
            $user->email = $data['email'];
            $user->password = $data['password'];
            $user->user_type = $data['user_type'];
            $user->area_id = $data['area_id'];
            $user->is_phone_verified = 1;
            $user->is_active = 1;
            $user->street_address = $data['street_address'];
            $user->country = $data['country'];
            $user->city = $data['city'];
            $user->save();
         return $user;
        }
    }

    public function view($id){
        return User::find($id);
    }


    public function delete($id){
        return User::find($id)->delete();
    }

    public function searchAccountNumber($id){
        return User::where('pharmacy_id','=',$id)->get()->first();//رمز الحساب
    }
}
