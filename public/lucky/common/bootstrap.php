<?php

/**
 * 启动文件,加载相关配置
 */

ini_set('display_errors', 'On');

if (defined('DE_BUG') && DE_BUG == 1) {
    ini_set('display_errors', 'On');
    error_reporting(E_ALL);
}

try {
    session_start();
} catch (Exception $e) {
    session_regenerate_id();
    session_start();
}

define('INT_SYS', TRUE);
define('ROOT_PATH', substr(dirname(__FILE__), 0, -6));
define('LIB_PATH', ROOT_PATH . 'libs/');
define('COMMON_PAH', ROOT_PATH . 'common/');
define('UTILS_PAH', LIB_PATH . 'utils/');

include(COMMON_PAH . 'config.php');
include(COMMON_PAH . 'function.php');
include(UTILS_PAH . 'MyPDO.class.php');
include(UTILS_PAH . 'RedisDriver.class.php');

$pdo = MyPDO::getInstance($db_config);

$redis = RedisDriver::getInstance($redis_config);

