<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 18-12-25
 * Time: 上午10:46
 * 获取相关配置信息
 */

$dirroot = $_SERVER['DOCUMENT_ROOT'];
require_once($dirroot . '/common/bootstrap.php');

$token = 'cwDAtlwdE3WBmZVv';
$_token = isset($_POST['_token']) ? $_POST['_token'] : '';
if ($token != $_token) {
    ajaxReturn(array('code' => -1, 'msg' => 'token is error'));
}

//获取当前相关配置信息
$sql = "SELECT * FROM lucky.lucky_config LIMIT 1";
$res = $pdo->query($sql, 'Row');
//当前抽奖轮数
$now_turn_id = $res['turn_id'];
//当前轮数每次抽多少用户
$now_pre_user = $res['turn_pre_num'];

if ($res) {
    ajaxReturn(array('code' => 1, 'msg' => 'success', 'data' => array('turn_id' => $now_turn_id, 'turn_pre_num' => $now_pre_user)));
}

ajaxReturn(array('code' => -1, 'msg' => 'error'));





