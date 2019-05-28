<?php

$dirroot = $_SERVER['DOCUMENT_ROOT'];
require_once($dirroot . '/lucky/common/bootstrap.php');

$sign = isset($_REQUEST['sign']) ? $_REQUEST['sign'] : '';
$time = isset($_REQUEST['time']) ? $_REQUEST['time'] : '';
$key = 'yW3HbG58mxoToBIN';
$_sign = md5($key . $time);
if ($sign != $_sign) {
    //ajaxReturn(array('status' => -1, 'msg' => 'sign is error'));
}

//清空redis数据
$sql = "SELECT * FROM lucky.lucky_turn";
$res = $pdo->query($sql);
if (is_array($res)) {
    foreach ($res as $val) {
        $del_key = 'turn_' . $val['turn_id'];
        $redis->del($del_key);
    }
}

//删除额外新增奖
$sql = "DELETE FROM lucky.lucky_turn WHERE turn_id>3";
$pdo->execSql($sql);

//清空中奖记录表
$sql = "TRUNCATE TABLE lucky.lucky_record";
$res = $pdo->execSql($sql);

ajaxReturn(array('status' => 1, 'msg' => 'flush success'));