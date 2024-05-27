<?php

namespace App\CPU;

use App\Model\BusinessSetting;
use Exception;
use Illuminate\Support\Facades\Http;

class SMS_module
{
    public static function send($receiver, $otp)
    {
        $config = self::get_settings('mtn_sms');
        if (isset($config) && $config['status'] == 1) {
            $response = self::SMS_MODULE_MTN($receiver, $otp);
            return $response;
        }

        $config = self::get_settings('syriatel_sms');
        if (isset($config) && $config['status'] == 1) {
            $response = self::SMS_MODULE_SYRIATEL($receiver, $otp);
            return $response;
        }

        return 'not_found';
    }

    public static function SMS_MODULE_MTN($receiver, $otp)
    {
        $config = self::get_settings('mtn_sms');
        if (isset($config) && $config['status'] == 1) {
            $url = $config['api_key'];
            $user_name = $config['user_name'];
            $password = $config['password'];
            $code_number = $config['code_number'];
            $from = $config['from'];
            $message='رمز التفعيل الخاص بك  '. $otp;
            $str=mb_strtoupper(bin2hex(mb_convert_encoding($message, 'UTF-16BE', 'UTF-8')));
            try {
              $base_url = $url . '?User=' . $user_name . '&Pass=' . $password . '&From=' . $from . '&Gsm=' . $code_number . '' . $receiver . '&Msg=' . $str . '&Lang=0';
              $response = Http::post($base_url);
              $jsonData = $response->status();
            } catch (Exception $exception) {
                $jsonData = "error";
            }
        }
        return $jsonData;
    }

    public static function SMS_MODULE_SYRIATEL($receiver, $otp)
    {
        $config = self::get_settings('syriatel_sms');
        $response = 'error';
        if (isset($config) && $config['status'] == 1) {
            $url = $config['api_key'];
            $user_name = $config['user_name'];
            $password = $config['password'];
            $code_number = $config['code_number'];
            $from = $config['from'];
            $message='رمز التفعيل الخاص بك  '. $otp;
            $str=mb_strtoupper(bin2hex(mb_convert_encoding($message, 'UTF-16BE', 'UTF-8')));
            try {
                $base_url = $url . '?user_name=' . $user_name . '&password=' . $password . '&sender=' . $from . '&to=' . $code_number . '' . $receiver . '&msg=' . $str;
                $response = Http::post($base_url);
                $jsonData = $response->status();
            } catch (Exception $exception) {
                $jsonData = 'error';
            }
        }
        return $jsonData;
    }

    public static function get_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['type' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

}
