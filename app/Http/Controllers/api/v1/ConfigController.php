<?php

namespace App\Http\Controllers\api\v1;

use App\CPU\Helpers;
use App\CPU\ProductManager;
use App\Http\Controllers\Controller;
use App\Model\Color;
use App\Model\Currency;
use App\Model\HelpTopic;
use App\Model\ShippingType;
use Illuminate\Support\Facades\Cache;
use App\Model\BusinessSetting;

class ConfigController extends Controller
{
      public function configuration()
    {
        $config = Cache::remember('configuration_data', $minutes = 60, function () {
            $currency = Currency::all();

            return [
                'system_default_currency' => (int) Helpers::get_business_settings('system_default_currency'),
                'digital_payment' => (bool) Helpers::get_business_settings('digital_payment')['status'] ?? false,
                'cash_on_delivery' => (bool) Helpers::get_business_settings('cash_on_delivery')['status'] ?? false,
                'company_name' => Helpers::get_business_settings('company_name'),
                'company_phone' => (int) Helpers::get_business_settings('company_phone'),
                'company_email' => Helpers::get_business_settings('company_email'),
                'facebook_link' => Helpers::get_business_settings('facebook_link'),
                'slink' => Helpers::get_business_settings('slink'),
                'company_address' => Helpers::get_business_settings('company_address'),
                'in_review' => (int) Helpers::get_business_settings('in_review'),
                'base_urls' => [
                    'product_image_url' => ProductManager::product_image_path('product'),
                    'product_thumbnail_url' => ProductManager::product_image_path('thumbnail'),
                    'brand_image_url' => asset('storage/app/public/brand'),
                    'customer_image_url' => asset('storage/app/public/profile'),
                    'banner_image_url' => asset('storage/app/public/banner'),
                    'bag_image_url' => asset('storage/app/public/bag'),
                    'category_image_url' => asset('storage/app/public/category'),
                    'review_image_url' => asset('storage/app/public'),
                    'seller_image_url' => asset('storage/app/public/seller'),
                    'shop_image_url' => asset('storage/app/public/shop'),
                    'notification_image_url' => asset('storage/app/public/notification'),
                ],
                'static_urls' => [
                    'contact_us' => route('contacts'),
                    'brands' => route('brands'),
                    'categories' => route('categories'),
                    'customer_account' => route('user-account'),
                ],
                'about_us' => Helpers::get_business_settings('about_us'),
                'privacy_policy' => Helpers::get_business_settings('privacy_policy'),
                'faq' => HelpTopic::all(),
                'terms_&_conditions' => Helpers::get_business_settings('terms_condition'),
                'currency_list' => $currency,
                'currency_symbol_position' => "right",
                'business_mode' => "multi",
                'maintenance_mode' => (bool) Helpers::get_business_settings('maintenance_mode') ?? false,
                'language' => [],
                'colors' => [],
                'unit' => Helpers::units(),
                'shipping_method' => "inhouse_shipping",
                'email_verification' => false,
                'phone_verification' => false,
                'country_code' => "SY",
                'social_login' => [],
                'currency_model' => "single_currency",
                'forgot_password_verification' => "email",
                'announcement' => Helpers::get_business_settings('announcement'),
                'pixel_analytics' => null,
                'software_version' => null,
                'decimal_point_settings' => null,
                'inhouse_selected_shipping_type' => "order_wise"
            ];
        });

        return response()->json($config);
    }
}

