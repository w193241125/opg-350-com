<?php

/**
 * 生成用户获奖列表
 */

$dirroot = $_SERVER['DOCUMENT_ROOT'];
require_once($dirroot . '/lucky/common/bootstrap.php');

$sign = isset($_REQUEST['sign']) ? $_REQUEST['sign'] : '';
$time = isset($_REQUEST['time']) ? $_REQUEST['time'] : '';
$key = 'oKMuiE8aAiqzeWpl';
$_sign = md5($key . $time);
if ($sign != $_sign) {
    //ajaxReturn(array('code' => -1, 'msg' => 'sign is error'));
}

//配置一等奖二等奖三等奖人数
$rank_array = array('first_prize' => 25, 'second_prize' => 15, 'third_prize' => 6);
$sum_people = array_sum($rank_array);
$sql = "SELECT COUNT(id) as total FROM lucky.lucky_record WHERE turn_id IN(1,2,3)";
$total_row = $pdo->query($sql, 'Row');
$total_num = $total_row['total'];
//判断如果生成过则终止，防止重复生成
if ($sum_people == $total_num) {
    ajaxReturn(array('code' => -1, 'msg' => 'prize rank has been generated'));
}

//***************************************员工分类型获取**************************************//
//获取所有用户id和其所对应的level等级
$sql = "SELECT id,level FROM lucky.lucky_user";
$all_data = $pdo->query($sql);
$all_user = array();    //所有用户ID所对应的level等级
foreach ($all_data as $val) {
    $all_user[$val['id']] = $val['level'];//用于插入用户等级
}

//获取所有老员工
$sql = "SELECT id FROM lucky.lucky_user WHERE level in (2,1)"; //获取所有level=2的用户
$level_onetwo_data = $pdo->query($sql);
$level_onetwo_arr = array();
foreach ($level_onetwo_data as $val) {
    $level_onetwo_arr[] = $val['id'];
}

//获取所有转正员工6个月以下
$sql = "SELECT id FROM lucky.lucky_user WHERE level=3"; //获取所有level=3的用户
$level_three_data = $pdo->query($sql);
$level_three_arr = array();
foreach ($level_three_data as $val) {
    $level_three_arr[] = $val['id'];
}

//获取所有未转正员工
$sql = "SELECT id FROM lucky.lucky_user WHERE level=4"; //获取所有level=4的用户
$level_four_data = $pdo->query($sql);
$level_four_arr = array();
foreach ($level_four_data as $val) {
    $level_four_arr[] = $val['id'];
}


//***************************************员工分类型获取结束**************************************//


//***************************************生成奖项**************************************//
$commit_flag_three = true;
$pdo->beginTrans();
//生成三等奖
for ($i=0;$i<$rank_array['third_prize'];$i++){
    $uid = array_rand($all_user);
    $winner[] = $uid;
    $sql = "INSERT INTO lucky.lucky_record(user_id, level,turn_id,rank,rank_mark) values ($uid, $all_user[$uid],3,3,'三等奖')";//记录三等奖中奖信息
    $res = $pdo->execSql($sql);
    if (!$res) $commit_flag_three = false;
    unset($all_user[$uid]);//删除所有中奖用户
}
//全部插入成功,执行提交操作
if ($commit_flag_three) {
    $pdo->commit();
} else {
    ajaxReturn(array('status' => -1, 'msg' => 'error three'));
}

//生成完三等奖后销毁所有非转正用户
foreach ($all_user as $k=>$v) {
    if (in_array($k,$level_four_arr)){
        unset($all_user[$k]);
    }
}

/****************生成二等奖***************/
$commit_flag_two = true;
$pdo->beginTrans();
//获取二等奖
for ($i=0;$i<$rank_array['second_prize'];$i++){
    $uid = array_rand($all_user);
    $winner[] = $uid;
    $sql = "INSERT INTO lucky.lucky_record(user_id, level,turn_id,rank,rank_mark) values ($uid, $all_user[$uid],2,2,'二等奖')";//记录二等奖中奖信息
    $res = $pdo->execSql($sql);
    if (!$res) $commit_flag_two = false;
    unset($all_user[$uid]);//删除所有中奖用户
}
//全部插入成功,执行提交操作
if ($commit_flag_two) {
    $pdo->commit();
} else {
    ajaxReturn(array('status' => -1, 'msg' => 'error two'));
}

//生成完三等奖后销毁所有非老用户
foreach ($all_user as $k=>$v) {
    if (in_array($k,$level_three_arr)){
        unset($all_user[$k]);
    }
}

$commit_flag_one = true;
$pdo->beginTrans();
//获取一等奖
for ($i=0;$i<$rank_array['first_prize'];$i++){
    $uid = array_rand($all_user);
    $winner[] = $uid;
    $sql = "INSERT INTO lucky.lucky_record(user_id, level,turn_id,rank,rank_mark) values ($uid, $all_user[$uid],1,1,'一等奖')";//记录一等奖中奖信息
    $res = $pdo->execSql($sql);
    if (!$res){
        $commit_flag_one = false;
    }
    unset($all_user[$uid]);//删除所有中奖用户
}
//全部插入成功,执行提交操作

if ($commit_flag_one) {
    $pdo->commit();
} else {
    ajaxReturn(array('status' => -1, 'msg' => 'error one'));
}


//***************************************生成奖项结束**************************************//


/*******************************************************************************/
/*******************************************************************************/
//将产生的抽奖数据放入redis缓存
$sql = "SELECT user_id,rank FROM lucky.lucky_record";
$all_of_data = $pdo->query($sql);
$all_prizes = array();
foreach ($all_of_data as $v) {
    $all_prizes[$v['rank']][] = $v['user_id'];
}

$redis->sAdd('turn_1', $all_prizes[1]);
$redis->sAdd('turn_2', $all_prizes[2]);
$redis->sAdd('turn_3', $all_prizes[3]);

ajaxReturn(array('status' => 1, 'msg' => 'generate success!'));
