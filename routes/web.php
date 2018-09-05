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


Auth::routes();

Route::get('/', function () {
    return view('welcome');
});



Route::get('/home', 'HomeController@index')->name('home');
Route::get('/', 'IndexController@index')->name('index');
Route::get('/admin', 'IndexController@index')->name('index');
Route::get('/test', 'IndexController@index')->name('test');


Route::group([ 'prefix' => 'system','namespace' => 'System', 'middleware'=>['role:menu']], function () {
    // 定制菜单,资源路由
    Route::resource('menu', 'MenuController');

    Route::get('/createMenu', 'MenuController@createMenu')->name('createMenu');
    // ajax获取二级菜单
    Route::get('getchildmenu', 'MenuController@ajaxGetChildMenu')->name('MenuController.ajaxGetChild');
    // 缓存保存菜单排序
    Route::post('savemenuorder', 'MenuController@saveOrder')->name('MenuController.saveMenuOrder');
});