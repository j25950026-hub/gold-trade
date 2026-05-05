<?php
// 抑制PHP 8.1+废弃警告（兼容ThinkPHP 5.0）
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);

// [ 应用入口文件 ]
define('APP_PATH', __DIR__ . '/../application/');
define('BIND_MODULE','api');

// 加载框架引导文件
require __DIR__ . '/../vendor/topthink/framework/start.php';
