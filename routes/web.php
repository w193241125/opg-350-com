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


Route::group([ 'middleware'=>['auth']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/', 'IndexController@index')->name('index');
    Route::get('/admin', 'IndexController@index')->name('index');
    Route::get('/test', 'IndexController@index')->name('test.index');
});


Route::group([ 'prefix' => 'system','namespace' => 'System', 'middleware'=>['CheckPermission','auth']], function () {
    Route::get('/game', 'IndexController@index')->name('game.index');
    // 定制菜单,资源路由
    Route::resource('menu', 'MenuController');
    // ajax获取二级菜单
    Route::get('getchildmenu', 'MenuController@ajaxGetChildMenu')->name('MenuController.ajaxGetChild');
    // 缓存保存菜单排序
    Route::post('savemenuorder', 'MenuController@saveOrder')->name('MenuController.saveMenuOrder');

    // 用户,资源路由
    Route::resource('user', 'UserController');
    //获取用户权限
    Route::get('getUserPermission/{uid}','UserController@getUserPermission')->name('getUserPermission');
    //更新用户权限
    Route::post('updUserPermission','UserController@updUserPermission')->name('updUserPermission');

    // 获取用户角色
    Route::get('getUserRoles/{uid}','UserController@getUserRoles')->name('UserController.getUserRoles');
    //更新用户角色
    Route::post('updUserRole','UserController@updUserRole')->name('updUserRole');

    // ajax获取职位
    Route::get('getposition', 'UserController@ajaxGetPosition')->name('UserController.ajaxGetPosition');
    // ajax 检查用户名是否存在
    Route::get('ajaxCheckUsername', 'UserController@ajaxCheckUsername')->name('UserController.ajaxCheckUsername');

    Route::get('game', 'IndexController@index')->name('game.index');

    //权限管理资源路由
    Route::resource('permission', 'PermissionController');
    // ajax 权限名是否存在
    Route::get('ajaxCheckPermission', 'PermissionController@ajaxCheckPermission')->name('PermissionController.ajaxCheckPermission');

    //角色管理资源路由
    Route::resource('role', 'RoleController');
    // ajax 角色是否存在
    Route::get('ajaxCheckRole', 'RoleController@ajaxCheckRole')->name('RoleController.ajaxCheckRole');


});


Route::group([ 'prefix' => 'operator','namespace' => 'Operator', 'middleware'=>['CheckPermission','auth']], function () {
    Route::get('queryFailedOrder','OperatorController@queryFailedOrder')->name('pay.queryFailedOrder');
    Route::post('bf','OperatorController@bf')->name('pay.bf');
    //数据按日统计
    Route::get('data_statistics_day','OperatorController@data_statistics_day')->name('data.data_statistics_day');
    Route::post('data_statistics_day','OperatorController@data_statistics_day')->name('data.data_statistics_day_post');
    //收入按渠道按游戏统计
    Route::get('incomeBABG','OperatorController@incomeBABG')->name('data.incomeBABG');
    Route::post('incomeBABG','OperatorController@incomeBABG')->name('data.incomeBABGPost');
});

