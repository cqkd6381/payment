<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

//微信PC端支付
Route::get('/pay/wxpay','OrdersController@wxPay');
Route::get('/pay/wxpay/success','OrdersController@wxPaySuccess');


//支付宝电脑端支付
Route::get('/pay/alipay','OrdersController@aliPay');
Route::get('/pay/alipay/success','OrdersController@aliPaySuccess');
Route::post('/payment/notify','OrdersController@notify');