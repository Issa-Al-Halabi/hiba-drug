<?php

namespace App\Providers;

use App\CPU\Helpers;
use App\Model\BusinessSetting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

use App\Repository\Pharmacy\PharmacyInterface;
use App\Repository\Pharmacy\PharmacyRepository;
use App\Repository\User\UserInterface;
use App\Repository\User\UserRepository;

ini_set('memory_limit',-1);
class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Amirami\Localizator\ServiceProvider::class);
        }
        $this->app->bind(UserInterface::class,UserRepository::class);
        $this->app->bind(PharmacyInterface::class,PharmacyRepository::class);
        $this->app->bind(AlameenInterface::class,AlameenRepository::class);
    }

    public function boot()
    {
        Paginator::useBootstrap();
        try {
            $web = BusinessSetting::all();
            $settings = Helpers::get_settings($web, 'colors');
            $data = json_decode($settings['value'], true);
            $web_config = [
                'primary_color' => $data['primary'],
                'secondary_color' => $data['secondary'],
                'name' => Helpers::get_settings($web, 'company_name'),
                'phone' => Helpers::get_settings($web, 'company_phone'),
                'web_logo' => Helpers::get_settings($web, 'company_web_logo'),
                'mob_logo' => Helpers::get_settings($web, 'company_mobile_logo'),
                'fav_icon' => Helpers::get_settings($web, 'company_fav_icon'),
                'email' => Helpers::get_settings($web, 'company_email'),
                'about' => Helpers::get_settings($web, 'about_us'),
                'footer_logo' => Helpers::get_settings($web, 'company_footer_logo'),
                'copyright_text' => Helpers::get_settings($web, 'company_copyright_text'),
            ];

            //language
            $language = BusinessSetting::where('type', 'language')->first();

            //currency
            \App\CPU\Helpers::currency_load();

            View::share(['web_config' => $web_config, 'language' => $language]);

            Schema::defaultStringLength(191);
        } catch (\Exception $ex) {

        }
    }
}
