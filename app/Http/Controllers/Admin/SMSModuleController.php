<?php

namespace App\Http\Controllers\Admin;

use App\CPU\Helpers;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SMSModuleController extends Controller
{
    public function sms_index()
    {
        return view('admin-views.business-settings.sms-index');
    }

    public function sms_update(Request $request, $module)
    {
        if ($module == 'mtn_sms') {
            DB::table('business_settings')->updateOrInsert(['type' => 'mtn_sms'], [
                'type' => 'mtn_sms',
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'user_name' => $request['user_name'],
                    'password' => $request['password'],
                    'code_number' => $request['code_number'],
                    'from' => $request['from'],
                    'otp_template' => $request['otp_template']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } elseif ($module == 'syriatel_sms') {
            DB::table('business_settings')->updateOrInsert(['type' => 'syriatel_sms'], [
                'type' => 'syriatel_sms',
                'value' => json_encode([
                    'status' => $request['status'],
                    'api_key' => $request['api_key'],
                    'user_name' => $request['user_name'],
                    'password' => $request['password'],
                    'code_number' => $request['code_number'],
                    'from' => $request['from'],
                    'otp_template' => $request['otp_template']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return back();
    }
}
