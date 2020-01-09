<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/','Index\IndexController@Index');  //网站首页



Route::any('/test/hello','Test\TestController@hello');
Route::any('/test/adduser','Test\TestController@adduser');
Route::any('/test/ord','Test\TestController@ord');
Route::any('/test/dec','Test\TestController@dec');

Route::get('/weixin','Weixin\WeixinController@weixin');
Route::post('/weixin','Weixin\WeixinController@receiv');
Route::get('/weixin/media','Weixin\WeixinController@getmedia');
Route::get('/weixin/info','Weixin\WeixinController@info');
Route::get('/token','Weixin\WeixinController@flushAccessToken');

//微信公众号
Route::get('/weixin/menu','Weixin\WeixinController@createmenu');

Route::get('/vote','VoteController@index');//微信投票
//微商城
Route::get('/goods/detail','Goods\IndexController@detail');
//微信扫码
Route::get('/weixin/qrcode','Weixin\QrcodeController@qrcode');






//接口
Route::get('/test/pay','TestController@alipay');
Route::get('/test/alipay/return','Alipay\PayController@aliReturn');
Route::post('/test/alipay/notify','Alipay\PayController@notify');

Route::get('/test/jiekou','Api\TestController@test');
Route::post('/test/reg','Api\TestController@reg');
Route::post('/test/login','Api\TestController@login');
Route::get('/test/list','Api\TestController@userlist')->middleware('filtrate');



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');



//加密
Route::get('/user/add','User\IndexController@addSSHKey1');
Route::post('/user/addkey','User\IndexController@addSSHKey2');
//解密
Route::get('/user/decrypt/data','User\IndexController@decrypt1');
Route::post('/user/decrypt/data','User\IndexController@decrypt2');

Route::get('/sign/online','Sign\IndexController@signOnline');
Route::post('/sign/online','Sign\IndexController@signOnline1');
