<?php

namespace App\Http\Controllers\api\v1\auth;

use App\User;
use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Traits\GeneralTrait;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

use Illuminate\Support\Facades\Validator;

class ForgotPassword extends Controller
{
    use GeneralTrait;

    public function reset_password_request(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:6',
        ]);
        $identity = $request['phone'];

        if ($validator->fails()) {
            return response()->json(['status' => 404, 'errors' => Helpers::error_processor($validator)], 404);
        }

        $verification_by = 'phone';
        DB::table('password_resets')->where('identity', $identity)->delete();

        if ($verification_by == 'phone') {
            $customer = User::where('phone', $identity)->first();
            if (isset($customer)) {
                $token = rand(100000, 999999);
                DB::table('password_resets')->insert([
                    'identity' => $customer['phone'],
                    'token' => $token,
                    'created_at' => now(),
                ]);
                $resultSend = SMS_module::send($customer->phone, $token);
                $details = [
                    'token' => $token,
                    'status' => $resultSend,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                return response()->json(['status' => 200, 'message' => $details], 200);
            }
        }

        return response()->json(['errors' => [
            ['status' => 404, 'code' => 'not-found', 'message' => 'user not found or Account has been suspended!']
        ]], 404);
    }

    public function otp_verification_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'otp' => 'required'
        ]);
        $identity = $request['phone'];

        if ($validator->fails()) {
            return response()->json(['status' => 404, 'errors' => Helpers::error_processor($validator)], 404);
        }
        $data = DB::table('password_resets')->where(['token' => $request['otp']])
            ->where('identity', $identity)
            ->first();

        if (isset($data)) {
            return response()->json(['status' => 200, 'message' => 'Otp_verified'], 200);
        }
        return response()->json(['status' => 404, 'errors' => 'otp_not_found'], 404);
    }

    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|same:confirm_password|min:8',
            'confirm_password' => 'required|min:8'
        ]);
        $identity = $request['phone'];
        if ($validator->fails()) {
            return response()->json(['status' => 404, 'errors' => Helpers::error_processor($validator)], 404);
        }
        $data = true;
        if ($data == true) {
            DB::table('users')->where('phone', $identity)
                ->update([
                    'password' => bcrypt(str_replace(' ', '', $request['password']))
                ]);
            DB::table('password_resets')
                ->where('identity', $identity)
                ->where(['token' => $request['otp']])->delete();
            return response()->json(['status' => 200, 'message' => 'Password changed successfully.'], 200);
        }
    }
}
