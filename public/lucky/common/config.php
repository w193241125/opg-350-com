<?php

//默认数据库配置
$db_config = array(
    'db_host' => 'localhost',
    'db_port' => 3306,
    'db_name' => 'lucky',
    'db_user' => 'root',
    'db_pass' => '123456',
    'db_charset' => 'utf8',
);

//默认redis配置
$redis_config = array('host' => '127.0.0.1', 'port' => '6379', 'auth' => '123456');

//cookie配置
$cookie_prefix = '350';
$cookie_domain = '.350.io';
$cookie_path = '/';

define('COOKIE_PRE', '350_');
define("COOKIE_DOMAIN", ".350.com");
define("COOKIE_PATH", '/');

//主站点
define('WEBURL', 'http://swlapi.350.com/');
define("DOMAIN", "swlapi.350.com");
//H5 站点
define('H5_WEBURL', 'http://h5.350.com/');
define("H5_DOMAIN", "h5.350.com");
//用户数据站点
define("PASSPROT_WEBURL", "http://passport.350.com/");
define("PASSPROT_DOMAIN", "passport.350.com");