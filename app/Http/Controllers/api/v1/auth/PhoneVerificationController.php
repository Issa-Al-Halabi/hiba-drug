<?php

namespace App\Http\Controllers\api\v1\auth;

use App\CPU\Helpers;
use App\CPU\SMS_module;
use App\Http\Controllers\Controller;
use App\Model\PhoneOrEmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function App\CPU\translate;
use App\Http\Traits\GeneralTrait;


class PhoneVerificationController extends Controller
{
    use GeneralTrait;
    public function check_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:9|max:9|unique:users,phone'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 404, 'errors' => Helpers::error_processor($validator)], 404);
        }
        $code = rand(100000, 999999);
        $phone = PhoneOrEmailVerification::where(['phone_or_email' => $request['phone']])->first();

        if (isset($phone) != false) {
            $phone->token = $code;
            $phone->save();
            $resultSend = SMS_module::send($request['phone'], $code);
            $details = [
                'phone_or_email' => $request['phone'],
                'token' => $code,
                'status' => $resultSend,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            return response()->json(['status' => 200, 'message' => $details], 200);
        } else {
            $phone = PhoneOrEmailVerification::updateOrCreate([
                'phone_or_email' => $request['phone'],
                'token' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $resultSend = SMS_module::send($request['phone'], $code);
            $details = [
                'phone_or_email' => $request['phone'],
                'token' => $code,
                'status' => $resultSend,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            return response()->json(['status' => 200, 'message' => $details], 200);
        }

    }

    public function verify_phone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:10|numeric',
            'otp' => 'required|min:6|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 404, 'errors' => Helpers::error_processor($validator)], 404);
        }
        $verify = PhoneOrEmailVerification::where(['phone_or_email' => $request['phone'], 'token' => $request['otp']])->first();
        if (isset($verify)) {
            try {
                $verify->delete();
                return response()->json(['status' => 200, 'message' => translate('otp_verified')], 200);
            } catch (\Exception $exception) {
                return response()->json(['status' => 404, 'errors' => translate('otp_not_verified')], 404);
            }
        } else {
            return response()->json(['status' => 404, 'errors' => translate('otp_not_found')], 404);
        }
    }

}
