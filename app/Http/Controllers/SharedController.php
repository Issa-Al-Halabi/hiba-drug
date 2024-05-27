<?php

namespace App\Http\Controllers;

use App\CPU\Helpers;
use App\CPU\ImageManager;
use App\Model\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class SharedController extends Controller
{
    public function lang($local)
    {
        $direction = 'ltr';
        $language = BusinessSetting::where('type', 'language')->first();
        foreach (json_decode($language['value'], true) as $key => $data) {
            if ($data['code'] == $local) {
                $direction = isset($data['direction']) ? $data['direction'] : 'ltr';
            }
        }
        Cache::forget(md5(url('/admin')));
        session()->forget('language_settings');
        Helpers::language_load();
        session()->put('local', $local);
        Session::put('direction', $direction);
        return redirect()->back();
    }
}
