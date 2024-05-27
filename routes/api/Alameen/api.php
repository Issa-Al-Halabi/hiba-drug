<?php

use Illuminate\Support\Facades\Route;


//link (1) : http://localhost:8000/api/Alameen/products/store
//link (2) : http://localhost:8000/api/Alameen/save-brands
//link (3) : http://localhost:8000/api/Alameen/save-stores
//link (4) : http://localhost:8000/api/Alameen/products/save-pharmices

//link (5) : http://localhost:8000/api/Alameen/get-orders
//link (6) : http://localhost:8000/api/Alameen/get-pharmices

Route::group(['namespace' => 'AlameenApi','prefix'=>'Alameen'], function () {


    //Import Data
    Route::group(['prefix' => 'products'], function () {
        Route::post('store', 'AlameenController@StoreProducts');
    });
    Route::post('/save-brands', 'AlameenController@StoreBrands');
    Route::post('/save-stores', 'AlameenController@SaveStores');
    Route::post('/save-pharmices', 'AlameenController@SavePharmices');

    Route::get('/get-orders', 'AlameenController@getOrders');



    //Export Data
   // Route::post('/get-orders', 'AlameenController@ExportOrders');
   // Route::post('/get-pharmices', 'AlameenController@ExportPharmices');


});



