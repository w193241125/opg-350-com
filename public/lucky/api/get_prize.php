<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 18-12-25
 * Time: 上午10:12
 * 根据当前轮数获取对应奖项结果
 */

$dirroot = $_SERVER['DOCUMENT_ROOT'];
require_once($dirroot . '/common/bootstrap.php');

$token = 'cwDAtlwdE3WBmZVv';
$_token = isset($_POST['_token']) ? $_POST['_token'] : '';
$turn_id = isset($_POST['turn_id']) ? $_POST['turn_id'] : '';   //当前的抽奖轮数
if ($token != $_token) {
    ajaxReturn(array('code' => -1, 'msg' => 'token is error'));
}
//根据当前的轮数从中随机pop出一个中奖用户
$turn_key = 'turn_' . $turn_id;

$res = $redis->sPop($turn_key);

ajaxReturn(array('code' => 1, 'msg' => 'success', 'data' => $res));