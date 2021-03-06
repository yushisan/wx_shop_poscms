<?php

header('Content-Type: text/html; charset=utf-8');

// 显示错误提示
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_STRICT);
function_exists('ini_set') && ini_set('display_errors', TRUE);
function_exists('ini_set') && ini_set('memory_limit', '1024M');

// 查询执行超时时间
function_exists('set_time_limit') && set_time_limit(30);

// 系统核心程序目录,支持自定义路径和改名
define('FCPATH', dirname(__FILE__).'/web/');

// web网站目录,表示index.php文件的目录
define('WEBPATH', dirname(__FILE__).'/');

// 是否开启dr_image函数功能，1表示开启
define('IS_FUNC_IMAGE', 0);

// 是否允许编辑后台模板
define('IS_EDIT_TPL', 0);

// web网站目录,表示index.php文件的目录
define('CACHEPATH', WEBPATH.'cache/');

// 该文件的名称
!defined('SELF') && define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// 后台管理标识
!defined('IS_ADMIN') && define('IS_ADMIN', FALSE);

if (PHP_SAPI === 'cli' || defined('STDIN')) {
    unset($_GET);
    $_GET['c'] = 'cron';
    $_GET['m'] = 'index';
    chdir(dirname(__FILE__));
}

// 执行主程序
require FCPATH.'Init.php';