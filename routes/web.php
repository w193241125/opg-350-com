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

    //抽奖配置信息表
    Route::get('lotterywinner', 'LotteryController@lotterywinner')->name('lotterywinner.index');
    Route::get('lotteryuser', 'LotteryController@user')->name('lotteryuser.index');
    Route::get('lottery', 'LotteryController@index')->name('lottery.index');
    Route::post('lotteryone', 'LotteryController@lotteryone')->name('lotteryone');
    Route::post('lotterytwo', 'LotteryController@lotterytwo')->name('lotterytwo');
    Route::post('lotterythree', 'LotteryController@lotterythree')->name('lotterythree');
    Route::get('ajaxcheckUid', 'LotteryController@ajaxcheckUid')->name('ajaxcheckUid');
    Route::get('ajaxGetTurn', 'LotteryController@ajaxGetTurn')->name('ajaxGetTurn');
    Route::get('ajaxGetTurns', 'LotteryController@ajaxGetTurns')->name('ajaxGetTurns');
    Route::get('ajaxGetMarkIfExist', 'LotteryController@ajaxGetMarkIfExist')->name('ajaxGetMarkIfExist');
    Route::get('oneKeyFlush', 'LotteryController@oneKeyFlush')->name('oneKeyFlush');
    Route::get('setPool', 'LotteryController@setPool')->name('setPool');
});


Route::group([ 'prefix' => 'operator','namespace' => 'Operator', 'middleware'=>['CheckPermission','auth']], function () {
    //失败订单扫描
    Route::get('queryFailedOrder','OperatorController@queryFailedOrder')->name('pay.queryFailedOrder');
    Route::post('queryFailedOrder','OperatorController@queryFailedOrder')->name('pay.queryFailedOrderPost');
    Route::post('bf','OperatorController@bf')->name('pay.bf');
    //充值支付列表
    Route::get('payListQuery','OperatorController@payListQuery')->name('pay.payListQuery');
    Route::post('payListQuery','OperatorController@payListQuery')->name('pay.payListQueryPost');
    Route::post('failOrderInsert','OperatorController@failOrderInsert')->name('pay.failOrderInsert');
    //数据按日统计
    Route::get('data_statistics_day','OperatorController@data_statistics_day')->name('data.data_statistics_day');
    Route::post('data_statistics_day','OperatorController@data_statistics_day')->name('data.data_statistics_day_post');
    //数据按日统计
    Route::get('ltv','OperatorController@ltv')->name('data.ltv');
    Route::post('ltv','OperatorController@ltv')->name('data.ltv');
    //收入按渠道按游戏统计
    Route::get('incomeBABG','OperatorController@incomeBABG')->name('data.incomeBABG');
    Route::post('incomeBABG','OperatorController@incomeBABG')->name('data.incomeBABGPost');
    //渠道数据总览
    Route::get('total','OperatorController@total')->name('hm_channel.total');
    Route::post('total','OperatorController@total')->name('hm_channel.total');
    //春节活动调试
    Route::get('other','OperaterController@test')->name('other');

    //活动中心
    Route::get('activity','ActivityController@index')->name('activity.index');
    Route::get('ajaxGetActivity','ActivityController@ajaxGetActivity')->name('ajaxGetActivity');
    Route::post('/activity_add','ActivityController@activity_add')->name('activity.activity_add');
    Route::post('/activity_upd','ActivityController@activity_upd')->name('activity.activity_upd');

    Route::get('/activity_award','ActivityController@activity_award')->name('activity.award');
    Route::get('ajaxGetAward','ActivityController@ajaxGetAward')->name('ajaxGetAward');
    Route::post('/award_upd','ActivityController@award_upd')->name('activity.award_upd');
    Route::post('/award_upds','ActivityController@award_upds')->name('activity.award_upds');
    Route::post('/award_add','ActivityController@award_add')->name('activity.award_add');
    Route::post('/award_bulk_add','ActivityController@award_bulk_add')->name('activity.award_bulk_add');
    Route::get('/award_list','ActivityController@award_list')->name('activity.award_list');
    Route::post('/award_list','ActivityController@award_list')->name('activity.paward_list');
    Route::post('/award_del','ActivityController@award_del')->name('activity.award_del');
    Route::get('/award_edit/{id}','ActivityController@award_edit')->name('activity.award_edit');
    //礼包码上传
    Route::post('/gift_bag_upload','ActivityController@gift_bag_upload')->name('activity.gift_bag_upload');
    Route::post('/gift_bag_time_change','ActivityController@gift_bag_time_change')->name('activity.gift_bag_time_change');

    //活动
    Route::get('/activity_list','ActivityController@activity_list')->name('activity.list');
    Route::post('/list','ActivityController@activity_list')->name('activity.plist');
    Route::get('/activity_edit/{id}','ActivityController@activity_edit')->name('activity.activity_edit');
    Route::post('/activity_upd_status','ActivityController@activity_upd_status')->name('activity.activity_upd_status');
    Route::post('/activity_upd','ActivityController@activity_upd')->name('activity.activity_upd');
    Route::post('/activity_del','ActivityController@activity_del')->name('activity.activity_del');
    //活动用户
    Route::get('/user_manage','ActivityController@user_manage')->name('activity.user_manage');
    Route::post('/add_user','ActivityController@add_user')->name('activity.add_user');
    Route::get('/user_list','ActivityController@user_list')->name('activity.guser_list');
    Route::post('/user_list','ActivityController@user_list')->name('activity.user_list');
    Route::post('/user_del','ActivityController@user_del')->name('activity.user_del');
    Route::get('/user_edit/{id}','ActivityController@user_edit')->name('activity.user_edit');
    Route::post('/user_upd','ActivityController@user_upd')->name('activity.user_upd');
    Route::post('/oneKeyFlush','ActivityController@oneKeyFlush')->name('activity.oneKeyFlush');
});

