<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
namespace think;

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 执行应用
Container::get('app')->run()->send();
