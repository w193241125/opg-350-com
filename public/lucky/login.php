<?php

$dirroot = $_SERVER['DOCUMENT_ROOT'];
require_once($dirroot . '/common/bootstrap.php');

header("Content-Type: text/html;charset=utf-8");

$email = isset($_REQUEST['email']) ? $_REQUEST['email'] : '';
$passport = isset($_REQUEST['passport']) ? $_REQUEST['passport'] : '';

if (empty($email) || empty($passport)) {
    echo "用户名或密码不能为空！";
    exit;
}

if ($email == '350@350.com' && $passport == 'swl350') {
    $_SESSION['user_name'] = '350game';
    header('Location:http://lucky.350.com/index.php');
    exit;
}

echo "用户名或者密码错误！";

