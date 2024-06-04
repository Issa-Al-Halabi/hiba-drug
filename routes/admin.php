<?php

use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Admin', 'prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::get('/', function () {
        return redirect()->route('admin.auth.login');
    });
  
   Route::get('/test', function () {
        return csrf_token();
    })->name('test');

    Route::get('/testdate2', function () {
 //echo Artisan::call('optimize:clear');
//phpinfo();
//date_default_timezone_set('Asia/Damascus');   
//phpinfo();
//echo date('Y-m-d H:i:s');

 });

    /*authentication*/
    Route::group(['namespace' => 'Auth', 'prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/code/captcha/{tmp}', 'LoginController@captcha')->name('default-captcha');
        Route::get('login', 'LoginController@login')->name('login');
        Route::post('login', 'LoginController@submit')->middleware('actch');
        Route::get('logout', 'LoginController@logout')->name('logout');
    });

    /*authenticated*/
    Route::group(['middleware' => ['admin']], function () {
        //dashboard routes
        Route::get('/', 'DashboardController@dashboard')->middleware('cached')->name('dashboard'); //previous dashboard route
        Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
            Route::get('/', 'DashboardController@dashboard')->middleware('cached')->name('index');
            Route::post('order-stats', 'DashboardController@order_stats')->name('order-stats');
            Route::post('business-overview', 'DashboardController@business_overview')->name('business-overview');
        });

        //system routes
        Route::get('search-function', 'SystemController@search_function')->name('search-function');
        Route::get('maintenance-mode', 'SystemController@maintenance_mode')->name('maintenance-mode');
        Route::get('/get-order-data', 'SystemController@order_data')->name('get-order-data');


        //system Role
        Route::group(['prefix' => 'custom-role', 'as' => 'custom-role.', 'middleware' => ['module:employee_section']], function () {
            Route::get('create', 'CustomRoleController@create')->name('create');
            Route::post('create', 'CustomRoleController@store')->name('store');
            Route::get('update/{id}', 'CustomRoleController@edit')->name('update');
            Route::post('update/{id}', 'CustomRoleController@update');
        });



        //profile
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('view', 'ProfileController@view')->name('view');
            Route::get('update/{id}', 'ProfileController@edit')->name('update');
            Route::post('update/{id}', 'ProfileController@update');
            Route::post('settings-password', 'ProfileController@settings_password_update')->name('settings-password');
        });



        //withdraw
        Route::group(['prefix' => 'withdraw', 'as' => 'withdraw.', 'middleware' => ['module:user_section']], function () {
            Route::post('update/{id}', 'WithdrawController@update')->name('update');
            Route::post('request', 'WithdrawController@w_request')->name('request');
            Route::post('status-filter', 'WithdrawController@status_filter')->name('status-filter');
        });



        Route::group(['prefix' => 'deal', 'as' => 'deal.', 'middleware' => ['module:marketing_section']], function () {
            Route::get('flash', 'DealController@flash_index')->name('flash');
            Route::post('flash', 'DealController@flash_submit');

            // feature deal
            Route::get('feature', 'DealController@feature_index')->name('feature');

            Route::get('day', 'DealController@deal_of_day')->name('day');
            Route::post('day', 'DealController@deal_of_day_submit');
            Route::post('day-status-update', 'DealController@day_status_update')->name('day-status-update');

            Route::get('day-update/{id}', 'DealController@day_edit')->name('day-update');
            Route::post('day-update/{id}', 'DealController@day_update');
            Route::get('day-delete/{id}', 'DealController@day_delete')->name('day-delete');

            Route::get('edit/{id}', 'DealController@feature_edit')->name('edit');

            Route::post('status-update', 'DealController@status_update')->name('status-update');
            Route::post('feature-status', 'DealController@feature_status')->name('feature-status');

            Route::post('featured-update', 'DealController@featured_update')->name('featured-update');
            Route::get('add-product/{deal_id}', 'DealController@add_product')->name('add-product');
            Route::post('add-product/{deal_id}', 'DealController@add_product_submit');
            Route::get('delete-product/{deal_product_id}', 'DealController@delete_product')->name('delete-product');
        });

        //employee
        Route::group(['prefix' => 'employee', 'as' => 'employee.', 'middleware' => ['module:employee_section']], function () {
            Route::get('add-new', 'EmployeeController@add_new')->name('add-new');
            Route::post('add-new', 'EmployeeController@store');
            Route::get('list', 'EmployeeController@list')->name('list');
            Route::get('update/{id}', 'EmployeeController@edit')->name('update');
            Route::post('update/{id}', 'EmployeeController@update');
            Route::get('status/{id}/{status}', 'EmployeeController@status')->name('status');
        });


        //category
        Route::group(['prefix' => 'category', 'as' => 'category.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'CategoryController@index')->name('view');
            Route::get('fetch', 'CategoryController@fetch')->name('fetch');
            Route::post('store', 'CategoryController@store')->name('store');
            Route::get('edit/{id}', 'CategoryController@edit')->name('edit');
            Route::post('update/{id}', 'CategoryController@update')->name('update');
            Route::post('delete', 'CategoryController@delete')->name('delete');
            Route::get('status/{id}/{home_status}', 'CategoryController@status')->name('status');
        });


        //sub-category    and     sub-sub-category
        Route::group(['prefix' => 'sub-category', 'as' => 'sub-category.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'SubCategoryController@index')->name('view');
            Route::get('fetch', 'SubCategoryController@fetch')->name('fetch');
            Route::post('store', 'SubCategoryController@store')->name('store');
            Route::post('edit', 'SubCategoryController@edit')->name('edit');
            Route::post('update', 'SubCategoryController@update')->name('update');
            Route::post('delete', 'SubCategoryController@delete')->name('delete');
        });


        Route::group(['prefix' => 'sub-sub-category', 'as' => 'sub-sub-category.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'SubSubCategoryController@index')->name('view');
            Route::get('fetch', 'SubSubCategoryController@fetch')->name('fetch');
            Route::post('store', 'SubSubCategoryController@store')->name('store');
            Route::post('edit', 'SubSubCategoryController@edit')->name('edit');
            Route::post('update', 'SubSubCategoryController@update')->name('update');
            Route::post('delete', 'SubSubCategoryController@delete')->name('delete');
            Route::post('get-sub-category', 'SubSubCategoryController@getSubCategory')->name('getSubCategory');
            Route::post('get-category-id', 'SubSubCategoryController@getCategoryId')->name('getCategoryId');
        });


        //Pharma brands
        Route::group(['prefix' => 'brand', 'as' => 'brand.', 'middleware' => ['module:product_management']], function () {
            Route::get('add-new', 'BrandController@add_new')->name('add-new');
            Route::post('add-new', 'BrandController@store');
            Route::get('list', 'BrandController@list')->name('list');
            Route::get('update/{id}', 'BrandController@edit')->name('update');
            Route::post('update/{id}', 'BrandController@update');
            Route::post('delete', 'BrandController@delete')->name('delete');
        });



        //Stores    -->>>Done
        Route::group(['prefix' => 'store', 'as' => 'store.', 'middleware' => ['module:product_management']], function () {
            Route::get('add-new', 'StoresController@add_new')->name('add-new');
            Route::post('add-new', 'StoresController@store');
            Route::get('list', 'StoresController@list')->name('list');
            Route::get('edit/{id}', 'StoresController@edit')->name('update');
            Route::post('update/{id}', 'StoresController@update');
            Route::post('delete', 'StoresController@delete')->name('delete');
        });



        //banner
        Route::group(['prefix' => 'banner', 'as' => 'banner.', 'middleware' => ['module:marketing_section']], function () {
            Route::post('add-new', 'BannerController@store')->name('store');
            Route::get('list', 'BannerController@list')->name('list');
            Route::post('delete', 'BannerController@delete')->name('delete');
            Route::post('status', 'BannerController@status')->name('status');
            Route::get('edit/{id}', 'BannerController@edit')->name('edit');
            Route::put('update/{id}', 'BannerController@update')->name('update');
        });

        //pharmacy
        Route::group(['prefix' => 'pharmacy', 'as' => 'pharmacy.', 'middleware' => ['module:product_management']], function () {
            Route::get('list/{status}', 'PharmacyController@list')->middleware('cached')->name('list');
            Route::post('delete', 'PharmacyController@destroy')->name('delete');

            Route::get('vip/{id}/{status}', 'PharmacyController@vip')->name('vip');

            Route::get('activation/{id}/{status}', 'PharmacyController@activation')->name('activation');

            Route::get('export/{id}', 'PharmacyController@generate_excel')->name('export');

            Route::get('bulk-import', 'PharmacyController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'PharmacyController@bulk_import_data')->name('bulk-import-excel');
            Route::get('bulk-export', 'PharmacyController@bulk_export_data')->name('bulk-export');

            Route::get('exports', 'PharmacyController@generate_excel_all_pharmacies')->name('exports-pharmacies');
        });

        Route::group(['prefix' => 'pharmacyImport', 'as' => 'pharmacyImport.', 'middleware' => ['module:product_management']], function () {
            Route::get('edit/{id}', 'PharmacyController@pharmacy_Import_edit')->name('edit');
            Route::post('update/{id}', 'PharmacyController@pharmacy_Import_update')->name('update');
            Route::delete('delete/{id}', 'PharmacyController@pharmacy_Import_destroy')->name('delete');
            Route::get('activation-export/{id}', 'PharmacyController@activation_export')->name('activation-export');
        });


        //attribute
        Route::group(['prefix' => 'attribute', 'as' => 'attribute.', 'middleware' => ['module:product_management']], function () {
            Route::get('view', 'AttributeController@index')->name('view');
            Route::get('fetch', 'AttributeController@fetch')->name('fetch');
            Route::post('store', 'AttributeController@store')->name('store');
            Route::get('edit/{id}', 'AttributeController@edit')->name('edit');
            Route::post('update/{id}', 'AttributeController@update')->name('update');
            Route::delete('delete', 'AttributeController@delete')->name('delete');
        });


        //marketing product
        Route::group(['prefix' => 'marketing', 'as' => 'marketing.', 'middleware' => ['module:marketing_section']], function () {
            Route::get('list', 'MarketingController@list')->name('list');
            Route::post('store', 'MarketingController@store')->name('store');
            Route::post('delete', 'MarketingController@delete')->name('delete');
        });


        Route::group(['prefix' => 'coupon', 'as' => 'coupon.', 'middleware' => ['module:marketing_section']], function () {
            Route::get('add-new', 'CouponController@add_new')->name('add-new')->middleware('actch');;
            Route::post('store-coupon', 'CouponController@store')->name('store-coupon');
            Route::get('update/{id}', 'CouponController@edit')->name('update')->middleware('actch');;
            Route::post('update/{id}', 'CouponController@update');
            Route::get('status/{id}/{status}', 'CouponController@status')->name('status');
            Route::delete('delete/{id}', 'CouponController@delete')->name('delete');
        });



        //social-login
        Route::group(['prefix' => 'social-login', 'as' => 'social-login.', 'middleware' => ['module:business_settings']], function () {
            Route::get('view', 'BusinessSettingsController@viewSocialLogin')->name('view');
            Route::post('update/{service}', 'BusinessSettingsController@updateSocialLogin')->name('update');
        });


        //currency
        Route::group(['prefix' => 'currency', 'as' => 'currency.', 'middleware' => ['module:business_settings']], function () {
            Route::get('view', 'CurrencyController@index')->name('view')->middleware('actch');;
            Route::get('fetch', 'CurrencyController@fetch')->name('fetch');
            Route::post('store', 'CurrencyController@store')->name('store');
            Route::get('edit/{id}', 'CurrencyController@edit')->name('edit');
            Route::post('update/{id}', 'CurrencyController@update')->name('update');
            Route::get('delete/{id}', 'CurrencyController@delete')->name('delete');
            Route::post('status', 'CurrencyController@status')->name('status');
            Route::post('system-currency-update', 'CurrencyController@systemCurrencyUpdate')->name('system-currency-update');
        });



        //support-ticket
        Route::group(['prefix' => 'support-ticket', 'as' => 'support-ticket.', 'middleware' => ['module:support_section']], function () {
            Route::get('view', 'SupportTicketController@index')->name('view');
            Route::post('status', 'SupportTicketController@status')->name('status');
            Route::get('single-ticket/{id}', 'SupportTicketController@single_ticket')->name('singleTicket');
            Route::post('single-ticket/{id}', 'SupportTicketController@replay_submit')->name('replay');
        });



        //notification
        Route::group(['prefix' => 'notification', 'as' => 'notification.', 'middleware' => ['module:marketing_section']], function () {
            Route::get('add-new', 'NotificationController@index')->name('add-new');
            Route::post('store', 'NotificationController@store')->name('store');
            Route::get('edit/{id}', 'NotificationController@edit')->name('edit');
            Route::post('update/{id}', 'NotificationController@update')->name('update');
            Route::post('status', 'NotificationController@status')->name('status');
            Route::post('delete', 'NotificationController@delete')->name('delete');
        });


        //reviews
        Route::group(['prefix' => 'reviews', 'as' => 'reviews.', 'middleware' => ['module:business_section']], function () {
            Route::get('list', 'ReviewsController@list')->name('list')->middleware('actch');;
        });


        //customer
        Route::group(['prefix' => 'customer', 'as' => 'customer.', 'middleware' => ['module:user_section']], function () {
            Route::get('list', 'CustomerController@customer_list')->name('list');
            Route::post('status-update', 'CustomerController@status_update')->name('status-update');
            Route::get('view/{user_id}', 'CustomerController@view')->name('view');
            Route::get('edit/{id}', 'CustomerController@edit')->name('edit');
            Route::post('update/{id}', 'CustomerController@update')->name('update');
            Route::delete('delete/{id}', 'CustomerController@delete')->name('delete');


            Route::get('groups/{cityId}', 'CustomerController@groups')->name('groups');
            Route::get('areas/{groupId}', 'CustomerController@areas')->name('areas');
        });


        ///Report
        Route::group(['prefix' => 'report', 'as' => 'report.', 'middleware' => ['module:report']], function () {
            Route::get('order', 'ReportController@order_index')->name('order');
            Route::get('earning', 'ReportController@earning_index')->name('earning');
            Route::any('set-date', 'ReportController@set_date')->name('set-date');
            //sale report inhouse
            Route::get('inhoue-product-sale', 'InhouseProductSaleController@index')->name('inhoue-product-sale');
            Route::get('generateExcel', 'InhouseProductSaleController@generateExcel')->name('generateExcel');
          	Route::get('generateExcelBag', 'InhouseProductSaleController@generateExcelBag')->name('generateExcelBag');
            Route::get('seller-product-sale', 'SellerProductSaleReportController@index')->name('seller-product-sale');
        });

        //stock
        Route::group(['prefix' => 'stock', 'as' => 'stock.', 'middleware' => ['module:business_section']], function () {
            //product stock report
            Route::get('product-stock', 'ProductStockReportController@index')->name('product-stock');
            Route::post('ps-filter', 'ProductStockReportController@filter')->name('ps-filter');
            //product in wishlist report
            Route::get('product-in-wishlist', 'ProductWishlistReportController@index')->name('product-in-wishlist');
            Route::post('piw-filter', 'ProductWishlistReportController@filter')->name('piw-filter');
        });


        //sellers
        Route::group(['prefix' => 'sellers', 'as' => 'sellers.', 'middleware' => ['module:user_section']], function () {
            Route::get('seller-add', 'SellerController@add_seller')->name('seller-add');
            Route::get('seller-list', 'SellerController@index')->name('seller-list');
            Route::get('order-list/{seller_id}', 'SellerController@order_list')->name('order-list');
            Route::get('product-list/{seller_id}', 'SellerController@product_list')->name('product-list');

            Route::get('order-details/{order_id}/{seller_id}', 'SellerController@order_details')->name('order-details');
            Route::get('verification/{id}', 'SellerController@view')->name('verification');
            Route::get('view/{id}/{tab?}', 'SellerController@view')->name('view');
            Route::post('update-status', 'SellerController@updateStatus')->name('updateStatus');
            Route::post('withdraw-status/{id}', 'SellerController@withdrawStatus')->name('withdraw_status');
            Route::get('withdraw_list', 'SellerController@withdraw')->name('withdraw_list');
            Route::get('withdraw-view/{withdraw_id}/{seller_id}', 'SellerController@withdraw_view')->name('withdraw_view');

            Route::post('sales-commission-update/{id}', 'SellerController@sales_commission_update')->name('sales-commission-update');
        });


        //product
        Route::group(['prefix' => 'product', 'as' => 'product.', 'middleware' => ['module:product_management']], function () {
            Route::get('add-new', 'ProductController@add_new')->name('add-new');
            Route::post('store', 'ProductController@store')->name('store');
            Route::get('remove-image', 'ProductController@remove_image')->name('remove-image');
            Route::post('status-update', 'ProductController@status_update')->name('status-update');
            Route::post('pure-price-status-update', 'ProductController@pure_price_status_update')->name('pure-price-status-update');
            Route::get('list/{type}', 'ProductController@list')->middleware('cached')->name('list');
            Route::get('stock-limit-list/{type}', 'ProductController@stock_limit_list')->name('stock-limit-list');
            Route::get('get-variations', 'ProductController@get_variations')->name('get-variations');
            Route::post('update-quantity', 'ProductController@update_quantity')->name('update-quantity');
            Route::get('edit/{id}', 'ProductController@edit')->name('edit');
            Route::post('update/{id}', 'ProductController@update')->name('update');
            Route::post('featured-status', 'ProductController@featured_status')->name('featured-status');
            Route::get('approve-status', 'ProductController@approve_status')->name('approve-status');
            Route::post('deny', 'ProductController@deny')->name('deny');
            Route::post('sku-combination', 'ProductController@sku_combination')->name('sku-combination');
            Route::get('get-categories', 'ProductController@get_categories')->name('get-categories');
            Route::delete('delete/{id}', 'ProductController@delete')->name('delete');
            Route::get('updated-product-list', 'ProductController@updated_product_list')->name('updated-product-list');
            Route::post('updated-shipping', 'ProductController@updated_shipping')->name('updated-shipping');

            Route::get('view/{id}', 'ProductController@view')->name('view');
            Route::get('bulk-import', 'ProductController@bulk_import_index')->name('bulk-import');
            Route::post('bulk-import', 'ProductController@bulk_import_data');
            Route::get('bulk-export', 'ProductController@bulk_export_data')->name('bulk-export');

            Route::post('bulk-import-price', 'ProductController@bulk_import_data_purchase_price')->name('bulk-import-price');
        });



        //Bags routes details
        Route::group(['prefix' => 'bag', 'as' => 'bag.', 'middleware' => ['module:product_management']], function () {

            //bags routes
            Route::get('list', 'BagController@bag_list')->name('list');
            Route::get('add-new', 'BagController@bag_add_new')->name('add-new');
            Route::post('store', 'BagController@bag_store')->name('store');


            Route::post('update/price', 'BagController@bag_update_price')->name('product-update-price');
            Route::get('edit/{id}', 'BagController@bag_edit')->name('edit');
            Route::post('update/{id}', 'BagController@bag_update')->name('update');

            Route::post('delete', 'BagController@bag_delete')->name('delete');

            //update price product bag
            Route::post('products/list/price/{id}', 'BagController@bag_product_price')->name('bag-product-get-price');
            //End

            Route::post('settings/store/{id}', 'BagController@bag_settings_store')->name('settings_store');
            Route::get('settings/{id}', 'BagController@bag_settings')->name('settings');

            //bag products routes
            Route::get('products/list/{id}', 'BagController@bag_products_list')->name('products-list');
            Route::post('products/store/{bag_id}', 'BagController@bag_products_store')->name('products-store');
            Route::post('products/delete', 'BagController@bag_products_delete')->name('products-delete');

            Route::post('status-update', 'BagController@status_update')->name('status-update');
        });


        Route::group(['prefix' => 'city', 'as' => 'city.', 'middleware' => ['module:business_section']], function () {
            //city Routs
            Route::get('list', 'CityController@city_list')->name('list');
            Route::post('store', 'CityController@city_store')->name('store');
            Route::post('delete', 'CityController@city_delete')->name('delete');
            Route::post('status-update', 'CityController@city_status_update')->name('status-update');    //Error fadi hi
            Route::get('edit/{id}', 'CityController@edit')->name('edit-city');
            Route::post('update', 'CityController@update')->name('update-city');

            //group Routs
            Route::get('groups/list/{id}', 'GroupController@city_groups_list')->name('group-list');
            Route::post('group/store/{city_id}', 'GroupController@group_store')->name('group-store');
            Route::post('group/delete', 'GroupController@group_delete')->name('group-delete');
            Route::get('groups/list/edit/{id}', 'GroupController@edit')->name('edit-group');
            Route::post('group/update', 'GroupController@update')->name('update-group');


            //Area Routs
            Route::get('areas/list/{id}', 'AreaController@group_areas_list')->name('area-list');
            Route::post('area/store/{group_id}', 'AreaController@area_store')->name('area-store');
            Route::post('area/delete', 'AreaController@area_delete')->name('area-delete');
            Route::get('areas/list/edit/{id}', 'AreaController@edit')->name('edit-area');
            Route::post('area/update', 'AreaController@update')->name('update-area');
        });


        Route::group(['prefix' => 'transaction', 'as' => 'transaction.', 'middleware' => ['module:business_section']], function () {
            Route::get('list', 'TransactionController@list')->name('list');
            Route::get('refund-list', 'RefundTransactionController@list')->name('refund-list');
        });


        //business-settings
        Route::group(['prefix' => 'business-settings', 'as' => 'business-settings.', 'middleware' => ['module:business_settings']], function () {
            Route::get('general-settings', 'BusinessSettingsController@index')->name('general-settings')->middleware('actch');;
            Route::get('update-language', 'BusinessSettingsController@update_language')->name('update-language');
            Route::get('about-us', 'BusinessSettingsController@about_us')->name('about-us');
            Route::post('about-us', 'BusinessSettingsController@about_usUpdate')->name('about-update');
            Route::post('update-info', 'BusinessSettingsController@updateInfo')->name('update-info');



            //Social Icon
            Route::get('social-media', 'BusinessSettingsController@social_media')->name('social-media');
            Route::get('fetch', 'BusinessSettingsController@fetch')->name('fetch');
            Route::post('social-media-store', 'BusinessSettingsController@social_media_store')->name('social-media-store');
            Route::post('social-media-edit', 'BusinessSettingsController@social_media_edit')->name('social-media-edit');
            Route::post('social-media-update', 'BusinessSettingsController@social_media_update')->name('social-media-update');
            Route::post('social-media-delete', 'BusinessSettingsController@social_media_delete')->name('social-media-delete');
            Route::post('social-media-status-update', 'BusinessSettingsController@social_media_status_update')->name('social-media-status-update');



            Route::get('terms-condition', 'BusinessSettingsController@terms_condition')->name('terms-condition');
            Route::post('terms-condition', 'BusinessSettingsController@updateTermsCondition')->name('update-terms');
            Route::get('privacy-policy', 'BusinessSettingsController@privacy_policy')->name('privacy-policy');
            Route::post('privacy-policy', 'BusinessSettingsController@privacy_policy_update')->name('update-privacy-policy');




            Route::get('fcm-index', 'BusinessSettingsController@fcm_index')->name('fcm-index');
            Route::post('update-fcm', 'BusinessSettingsController@update_fcm')->name('update-fcm');


            //captcha
            Route::get('captcha', 'BusinessSettingsController@recaptcha_index')->name('captcha');
            Route::post('recaptcha-update', 'BusinessSettingsController@recaptcha_update')->name('recaptcha_update');

            //google map api
            Route::get('map-api', 'BusinessSettingsController@map_api')->name('map-api');
            Route::post('map-api-update', 'BusinessSettingsController@map_api_update')->name('map-api-update');

            Route::post('update-fcm-messages', 'BusinessSettingsController@update_fcm_messages')->name('update-fcm-messages');

            //refund request
            Route::group(['prefix' => 'refund', 'as' => 'refund.'], function () {
                Route::get('list/{status}', 'RefundController@list')->name('list');
                Route::get('details/{id}', 'RefundController@details')->name('details');
                Route::get('inhouse-order-filter', 'RefundController@inhouse_order_filter')->name('inhouse-order-filter');
                Route::post('refund-status-update', 'RefundController@refund_status_update')->name('refund-status-update');
            });



            Route::group(['prefix' => 'shipping-method', 'as' => 'shipping-method.', 'middleware' => ['module:business_settings']], function () {
                Route::get('by/admin', 'ShippingMethodController@index_admin')->name('by.admin');
                Route::get('by/seller', 'ShippingMethodController@index_seller')->name('by.seller');
                Route::post('add', 'ShippingMethodController@store')->name('add');
                Route::get('edit/{id}', 'ShippingMethodController@edit')->name('edit');
                Route::put('update/{id}', 'ShippingMethodController@update')->name('update');
                Route::post('delete', 'ShippingMethodController@delete')->name('delete');
                Route::post('status-update', 'ShippingMethodController@status_update')->name('status-update');
                Route::get('setting', 'ShippingMethodController@setting')->name('setting');
                Route::post('shipping-store', 'ShippingMethodController@shippingStore')->name('shipping-store');
            });

            Route::group(['prefix' => 'shipping-type', 'as' => 'shipping-type.', 'middleware' => ['module:business_settings']], function () {
                Route::post('store', 'ShippingTypeController@store')->name('store');
            });


            Route::group(['prefix' => 'category-shipping-cost', 'as' => 'category-shipping-cost.', 'middleware' => ['module:business_settings']], function () {
                Route::post('store', 'CategoryShippingCostController@store')->name('store');
            });


            Route::group(['prefix' => 'language', 'as' => 'language.', 'middleware' => ['module:business_settings']], function () {
                Route::get('', 'LanguageController@index')->name('index');
                //                Route::get('app', 'LanguageController@index_app')->name('index-app');
                Route::post('add-new', 'LanguageController@store')->name('add-new');
                Route::get('update-status', 'LanguageController@update_status')->name('update-status');
                Route::get('update-default-status', 'LanguageController@update_default_status')->name('update-default-status');
                Route::post('update', 'LanguageController@update')->name('update');
                Route::get('translate/{lang}', 'LanguageController@translate')->name('translate');
                Route::post('translate-submit/{lang}', 'LanguageController@translate_submit')->name('translate-submit');
                Route::post('remove-key/{lang}', 'LanguageController@translate_key_remove')->name('remove-key');
                Route::get('delete/{lang}', 'LanguageController@delete')->name('delete');
            });


            Route::group(['prefix' => 'mail', 'as' => 'mail.', 'middleware' => ['module:web_&_app_settings']], function () {
                Route::get('', 'MailController@index')->name('index')->middleware('actch');
                Route::post('update', 'MailController@update')->name('update');
                Route::post('update-sendgrid', 'MailController@update_sendgrid')->name('update-sendgrid');
            });

            Route::group(['prefix' => 'web-config', 'as' => 'web-config.', 'middleware' => ['module:web_&_app_settings']], function () {
                Route::get('/', 'BusinessSettingsController@companyInfo')->name('index')->middleware('actch');;
                Route::post('update-colors', 'BusinessSettingsController@update_colors')->name('update-colors');
                Route::post('update-language', 'BusinessSettingsController@update_language')->name('update-language');
                Route::post('update-company', 'BusinessSettingsController@updateCompany')->name('company-update');
                Route::post('update-company-email', 'BusinessSettingsController@updateCompanyEmail')->name('company-email-update');
                Route::post('update-company-phone', 'BusinessSettingsController@updateCompanyPhone')->name('company-phone-update');
                Route::post('upload-web-logo', 'BusinessSettingsController@uploadWebLogo')->name('company-web-logo-upload');
                Route::post('upload-mobile-logo', 'BusinessSettingsController@uploadMobileLogo')->name('company-mobile-logo-upload');
                Route::post('upload-footer-log', 'BusinessSettingsController@uploadFooterLog')->name('company-footer-logo-upload');
                Route::post('upload-fav-icon', 'BusinessSettingsController@uploadFavIcon')->name('company-fav-icon');
                Route::post('update-company-copyRight-text', 'BusinessSettingsController@updateCompanyCopyRight')->name('company-copy-right-update');
                Route::post('app-store/{name}', 'BusinessSettingsController@update')->name('app-store-update');
                Route::get('currency-symbol-position/{side}', 'BusinessSettingsController@currency_symbol_position')->name('currency-symbol-position');
                Route::post('shop-banner', 'BusinessSettingsController@shop_banner')->name('shop-banner');

                Route::get('db-index', 'DatabaseSettingController@db_index')->name('db-index');
                Route::post('db-clean', 'DatabaseSettingController@clean_db')->name('clean-db');

                Route::get('environment-setup', 'EnvironmentSettingsController@environment_index')->name('environment-setup');
                Route::post('update-environment', 'EnvironmentSettingsController@environment_setup')->name('update-environment');

                Route::get('refund-index', 'RefundController@index')->name('refund-index');
                Route::post('refund-update', 'RefundController@update')->name('refund-update');
            });

            Route::group(['prefix' => 'seller-settings', 'as' => 'seller-settings.', 'middleware' => ['module:business_settings']], function () {
                Route::get('/', 'BusinessSettingsController@seller_settings')->name('index')->middleware('actch');;
                Route::post('update-seller-settings', 'BusinessSettingsController@sales_commission')->name('update-seller-settings');
                Route::post('update-seller-registration', 'BusinessSettingsController@seller_registration')->name('update-seller-registration');
                Route::post('seller-pos-settings', 'BusinessSettingsController@seller_pos_settings')->name('seller-pos-settings');
                Route::get('business-mode-settings/{mode}', 'BusinessSettingsController@business_mode_settings')->name('business-mode-settings');
                Route::post('product-approval', 'BusinessSettingsController@product_approval')->name('product-approval');
            });


            Route::group(['prefix' => 'payment-method', 'as' => 'payment-method.', 'middleware' => ['module:business_settings']], function () {
                Route::get('/', 'PaymentMethodController@index')->name('index')->middleware('actch');
                Route::post('{name}', 'PaymentMethodController@update')->name('update');
            });

            Route::get('sms-module', 'SMSModuleController@sms_index')->name('sms-module');
            Route::post('sms-module-update/{sms_module}', 'SMSModuleController@sms_update')->name('sms-module-update');


            //analytics
            Route::get('analytics-index', 'BusinessSettingsController@analytics_index')->name('analytics-index');
            Route::post('analytics-update', 'BusinessSettingsController@analytics_update')->name('analytics-update');
        });


        Route::group(['prefix' => 'bonuses', 'as' => 'bonuses.'], function () {
            // Bonuses
            Route::get('get_salve_products', 'BounusController@get_salve_products')->name('get_salve_products');
            Route::get('get_main_products', 'BounusController@get_main_products')->name('get_main_products');
            Route::get('list', 'BounusController@index')->name('bonuses_list');
            Route::get('create', 'BounusController@create')->name('create');
            Route::post('store', 'BounusController@store')->name('store');
            Route::post('delete', 'BounusController@destroy')->name('delete');
            Route::post('delete_sec', 'BounusController@destroy_sec')->name('delete_sec');
        });


        Route::group(['prefix' => 'points', 'as' => 'points.'], function () {
            // Product Points
            Route::get('list', 'ProductPointController@index')->name('points_list');
            Route::get('create', 'ProductPointController@create')->name('points_create');
            Route::post('store', 'ProductPointController@store')->name('points_store');
            Route::post('delete', 'ProductPointController@destroy')->name('points_delete');
            Route::get('edit/{id}', 'ProductPointController@edit')->name('points_edit');
            Route::post('update/{id}', 'ProductPointController@update')->name('points_update');
            // Bag Points
            Route::get('bag_point_list', 'ProductPointController@bag_point_index')->name('bag_points_list');
            Route::get('bag_point_create', 'ProductPointController@bag_point_create')->name('bag_points_create');
            Route::post('bag_point_store', 'ProductPointController@bag_point_store')->name('bag_points_store');
            Route::post('bag_point_delete', 'ProductPointController@bag_point_destroy')->name('bag_points_delete');
            Route::get('bag_point_edit/{id}', 'ProductPointController@bag_point_edit')->name('bag_points_edit');
            Route::post('bag_point_update/{id}', 'ProductPointController@bag_point_update')->name('bag_points_update');
            // Order Points
            Route::get('order_points', 'ProductPointController@order_points')->name('order_points');
            Route::post('order_points_delele', 'ProductPointController@order_points_destroy')->name('order_points_delele');
            Route::post('order_points_store', 'ProductPointController@order_points_store')->name('order_points_store');
            // Pharmacies Points
            Route::get('pharmacies_points', 'ProductPointController@pharmacies_points')->name('pharmacies_points');
        });


        //order management
        Route::group(['prefix' => 'orders', 'as' => 'orders.', 'middleware' => ['module:order_management']], function () {
            Route::get('list/{status}', 'OrderController@list')->name('list');
          	Route::get('list/{status}/generate_excel_report', 'OrderController@generate_excel_report')->name('generate_excel_report');
            Route::get('details/{id}', 'OrderController@details')->name('details');
            Route::post('status', 'OrderController@status')->name('status');
            Route::post('payment-status', 'OrderController@payment_status')->name('payment-status');
            Route::post('productStatus', 'OrderController@productStatus')->name('productStatus');
            Route::get('generate-invoice/{id}', 'OrderController@generate_invoice')->name('generate-invoice');

            Route::get('inhouse-order-filter', 'OrderController@inhouse_order_filter')->name('inhouse-order-filter');


            Route::post('edit/order/product/{id}', 'OrderController@product_edit_order')->name('product-edit-order');


            Route::post('details/bagsProducts/{id}', 'BagController@products_bag_ajax')->name('product-bag-ajax');


            Route::get('edit/order/{id}', 'OrderController@edit_order')->name('edit-order');
            Route::post('update/order', 'OrderController@update_order')->name('update-order');
            Route::post('insert/order', 'OrderController@insert_order')->name('insert-order');
            Route::post('insert/order/bag', 'OrderController@insert_order_bag')->name('insert-order-bag');

            Route::post('delete-product', 'OrderController@delete_product_order')->name('delete-product');


            Route::post('update-deliver-info', 'OrderController@update_deliver_info')->name('update-deliver-info');
            Route::get('add-delivery-man/{order_id}/{d_man_id}', 'OrderController@add_delivery_man')->name('add-delivery-man');

            Route::get('add-pharmacy-man/{order_id}/{d_man_id}', 'OrderController@add_pharmacy_man')->name('add-pharmacy-man');

            //generate excel file
            Route::get('generate-excel/all', 'OrderController@generate_excel_all')->name('generate-excel-all');
            Route::get('generate-excel/{order_id}', 'OrderController@generate_excel')->name('generate-excel');

            Route::get('list/pharmacy/details/{order_id}', 'OrderController@show_order_details')->name('show-order-details');
        });


        //pos management
        Route::group(['prefix' => 'pos', 'as' => 'pos.', 'middleware' => ['module:pos_management']], function () {
            Route::get('/', 'POSController@index')->name('index');
            Route::get('quick-view', 'POSController@quick_view')->name('quick-view');
            Route::post('variant_price', 'POSController@variant_price')->name('variant_price');
            Route::post('add-to-cart', 'POSController@addToCart')->name('add-to-cart');
            Route::post('remove-from-cart', 'POSController@removeFromCart')->name('remove-from-cart');
            Route::post('cart-items', 'POSController@cart_items')->name('cart_items');
            Route::post('update-quantity', 'POSController@updateQuantity')->name('updateQuantity');
            Route::post('empty-cart', 'POSController@emptyCart')->name('emptyCart');
            Route::post('tax', 'POSController@update_tax')->name('tax');
            Route::post('discount', 'POSController@update_discount')->name('discount');
            Route::get('customers', 'POSController@get_customers')->name('customers');
            Route::post('order', 'POSController@place_order')->name('order');
            Route::get('orders', 'POSController@order_list')->name('orders');
            Route::get('order-details/{id}', 'POSController@order_details')->name('order-details');
            Route::get('invoice/{id}', 'POSController@generate_invoice');
            Route::any('store-keys', 'POSController@store_keys')->name('store-keys');
            Route::get('search-products', 'POSController@search_product')->name('search-products');


            Route::post('coupon-discount', 'POSController@coupon_discount')->name('coupon-discount');
            Route::get('change-cart', 'POSController@change_cart')->name('change-cart');
            Route::get('new-cart-id', 'POSController@new_cart_id')->name('new-cart-id');
            Route::post('remove-discount', 'POSController@remove_discount')->name('remove-discount');
            Route::get('clear-cart-ids', 'POSController@clear_cart_ids')->name('clear-cart-ids');
            Route::get('get-cart-ids', 'POSController@get_cart_ids')->name('get-cart-ids');

            Route::post('customer-store', 'POSController@customer_store')->name('customer-store');
        });


        Route::group(['prefix' => 'helpTopic', 'as' => 'helpTopic.', 'middleware' => ['module:web_&_app_settings']], function () {
            Route::get('list', 'HelpTopicController@list')->name('list');
            Route::post('add-new', 'HelpTopicController@store')->name('add-new');
            Route::get('status/{id}', 'HelpTopicController@status');
            Route::get('edit/{id}', 'HelpTopicController@edit');
            Route::post('update/{id}', 'HelpTopicController@update');
            Route::post('delete', 'HelpTopicController@destroy')->name('delete');
        });



        Route::group(['prefix' => 'contact', 'as' => 'contact.', 'middleware' => ['module:support_section']], function () {
            Route::post('contact-store', 'ContactController@store')->name('store');
            Route::get('list', 'ContactController@list')->name('list');
            Route::post('delete', 'ContactController@destroy')->name('delete');
            Route::get('view/{id}', 'ContactController@view')->name('view');
            Route::post('update/{id}', 'ContactController@update')->name('update');
            Route::post('send-mail/{id}', 'ContactController@send_mail')->name('send-mail');
        });


        // add middleware(distribution_management) --> controller
        Route::group(['prefix' => 'delivery-man', 'as' => 'delivery-man.', 'middleware' => ['module:distribution_management']], function () {
            Route::get('add', 'DeliveryManController@index')->name('add');
            Route::post('store', 'DeliveryManController@store')->name('store');
            Route::get('list', 'DeliveryManController@list')->name('list');
            Route::get('preview/{id}', 'DeliveryManController@preview')->name('preview');
            Route::get('edit/{id}', 'DeliveryManController@edit')->name('edit');
            Route::post('update/{id}', 'DeliveryManController@update')->name('update');
            Route::delete('delete/{id}', 'DeliveryManController@delete')->name('delete');
            Route::post('search', 'DeliveryManController@search')->name('search');
            Route::post('status-update', 'DeliveryManController@status')->name('status-update');

            Route::get('reviews', 'DeliveryManController@reviewList')->name('delivery-reviews');
            Route::post('store/review', 'DeliveryManController@store_review')->name('store-review');
        });


        //New Group delivery-trip
        Route::group(['prefix' => 'delivery-trip', 'as' => 'delivery-trip.', 'middleware' => ['module:distribution_management']], function () {

            Route::get('scheduling/{status}', 'DeliveryManController@scheduling_index')->name('scheduling');
            Route::get('edit/{id}', 'DeliveryManController@scheduling_edit')->name('scheduling-edit');
            Route::post('update/{id}', 'DeliveryManController@scheduling_update')->name('scheduling-update');

            Route::get('changeScheduling/{id}/{status}', 'DeliveryManController@changeScheduling')->name('scheduling-change');

            Route::get('scheduling-list', 'DeliveryManController@scheduling_list')->name('scheduling-list');
        });



        // add middleware(sales_management) --> controller
        Route::group(['prefix' => 'sales-man', 'as' => 'sales-man.', 'middleware' => ['module:sales_man_management']], function () {
            Route::get('add', 'SalesManController@index')->name('add');
            Route::post('store', 'SalesManController@store')->name('store');
            Route::get('list', 'SalesManController@list')->name('list');
            Route::delete('delete/{id}', 'SalesManController@destroy')->name('delete');
            Route::post('search', 'SalesManController@search')->name('search');
            Route::get('edit/{id}', 'SalesManController@edit')->name('edit');
            Route::post('update/{id}', 'SalesManController@update')->name('update');
            Route::get('preview/{id}', 'SalesManController@preview')->name('preview');

            Route::get('reviews', 'SalesManController@reviews')->name('salers-reviews');
            Route::post('store/review', 'SalesManController@store_review')->name('store-review');
            Route::get('review/{saler_id}', 'SalesManController@review')->name('review');


            Route::get('orders/team', 'SalesManController@orders_report_team')->name('orders-report-teams');
            Route::any('team/set-date', 'SalesManController@set_date')->name('team-set-date');

            //Work Plan
            Route::get('work-plans/list', 'WorkPlanController@work_plans_list')->name('work-plans');
            Route::get('work-plan/add', 'WorkPlanController@work_plan_add')->name('work-plan-add');
            Route::post('work-plan/store', 'WorkPlanController@work_plan_store')->name('work-plan-store');
            Route::delete('work-plan/delete/{id}', 'WorkPlanController@work_plan_delete')->name('work-plan-delete');
            Route::get('work-plan/edit/{id}', 'WorkPlanController@work_plan_edit')->name('work-plan-edit');
            Route::post('work-plan/update/{id}', 'WorkPlanController@work_plan_update')->name('work-plan-update');
            Route::post('work-plan/activation', 'WorkPlanController@work_plan_activation')->name('work-plan-activation');
            Route::get('work-plan/get/pharmacies/{saler_id}', 'WorkPlanController@work_plan_pharmacies')->name('work-plan-pharmacies');
            Route::get('work-plan/get/details/{plan_id}', 'WorkPlanController@work_plan_details')->name('work-plan-details');


            Route::get('work-plan/tasks/{id}', 'WorkPlanController@work_plan_tasks')->name('work-plan-tasks');
            Route::any('work-plan/refresh/{plan_id}', 'WorkPlanController@work_plan_refresh')->name('work-plan-refresh');
            Route::post('work-plan/tasks/import/file', 'WorkPlanController@plan_task_import')->name('tasks-import-file');
            Route::post('work-plan/task/store/{id}', 'WorkPlanController@work_plan_task_store')->name('work-plan-task-store');
            Route::get('work-plan/report', 'WorkPlanController@work_plans_report')->name('work-plans-report');
            Route::any('plan/set-date', 'WorkPlanController@plan_set_date')->name('plan-set-date');
          	Route::get('plan/generate_excel_report', 'WorkPlanController@generate_excel_report')->name('generate_excel_report');
            Route::post('work-plan/plan/report/details/{id}', 'WorkPlanController@plan_details_report')->name('plan-details-report');

            Route::get('work-plan/plan/archive/remove/{id}', 'WorkPlanController@plan_archive_remove')->name('plan-archive-remove');
            Route::post('work-plan/insert/pharmacy', 'WorkPlanController@insert_pharmacy_plan')->name('plan-pharmacy-insert');

            Route::get('areas/{catId}', 'SalesManController@areas')->name('areas');
            Route::post('unassign/{id}', 'SalesManController@unassign')->name('unassign');
            Route::post('assign/{id}', 'SalesManController@assign')->name('assign');


            Route::post('unassign-area/{id}', 'SalesManController@unassign_area')->name('unassign-area');
            Route::post('assign-area/{id}', 'SalesManController@assign_area')->name('assign-area');


            Route::post('unassign-group/{id}', 'SalesManController@unassign_group')->name('unassign-group');
            Route::post('assign-group/{id}', 'SalesManController@assign_group')->name('assign-group');

            Route::get('pharmacies/assigned', 'SalesManController@pharmacies_assigned')->name('pharmacies-assigned');

        });

        // sales man report
        Route::group(['prefix' => 'sales-report', 'as' => 'sales-report.', 'middleware' => ['module:sales_man_management']], function () {
            Route::get('team', 'SalesManReportController@getTeamReport')->name('team');
            Route::get('team/{team}/sellers', 'SalesManReportController@getSellersOfTeamReport')->name('sellers-team');
            Route::get('orders/team/{team}', 'SalesManReportController@getOrdersTeamReport')->name('orders-team');
            Route::get('orders/team/ajax/{team}', 'SalesManReportController@getOrdersTeamReportAjax')->name('orders-team-ajax');
            Route::get('orders/team/seller/{sellerId}', 'SalesManReportController@getOrdersSellerOfTeamReport')->name('orders-seller-team');

        });


        Route::group(['prefix' => 'file-manager', 'as' => 'file-manager.'], function () {
            Route::get('/download/{file_name}', 'FileManagerController@download')->name('download');
            Route::get('/index/{folder_path?}', 'FileManagerController@index')->name('index');
            Route::post('/image-upload', 'FileManagerController@upload')->name('image-upload');
            Route::delete('/delete/{file_path}', 'FileManagerController@destroy')->name('destroy');
        });
      /*
        Route::group(['prefix' => 'reward-item', 'as' => 'reward-item.'], function () {

            Route::get('/show/product', 'RewardItemController@showProduct')->name('showProduct');
            Route::get('/show/bag', 'RewardItemController@showBag')->name('showBag');

            Route::post('/store/product', 'RewardItemController@storeProduct')->name('storeProduct');
            Route::post('/store/bag', 'RewardItemController@storeBag')->name('storeBag');

            Route::post('/update/product/{id}', 'RewardItemController@updateProduct')->name('updateProduct');
            Route::post('/update/bag/{id}', 'RewardItemController@updateBag')->name('updateBag');

            Route::post('/delete/{id}', 'RewardItemController@destroy')->name('destroy');
        });
        */
         Route::group(['prefix' => 'reward-item', 'as' => 'reward-item.'], function () {

            Route::get('/show/product', 'RewardItemController@showProduct')->name('showProduct');
            Route::get('/show/bag', 'RewardItemController@showBag')->name('showBag');

            Route::post('/store/product', 'RewardItemController@storeProduct')->name('storeProduct');
            Route::get('/store/product', 'RewardItemController@addProduct')->name('addProduct');
            Route::post('/store/bag', 'RewardItemController@storeBag')->name('storeBag');
            Route::get('/store/bag', 'RewardItemController@addBag')->name('addBag');

            Route::get('/update/product/{id}', 'RewardItemController@showUpdateProduct')->name('showUpdateProduct');
            Route::post('/update/product/{id}', 'RewardItemController@updateProduct')->name('updateProduct');
            Route::get('/update/bag/{id}', 'RewardItemController@showUpdatebag')->name('showUpdateBag');
            Route::post('/update/bag/{id}', 'RewardItemController@updateBag')->name('updateBag');

            Route::delete('/delete/product/{id}', 'RewardItemController@destroyProduct')->name('destroyProduct');
            Route::delete('/delete/bag/{id}', 'RewardItemController@destroyBag')->name('destroyBag');
        });

    });


});
