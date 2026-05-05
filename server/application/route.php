<?php
// 路由定义
use think\Route;

// API路由 - 登录
Route::rule('api/login/login', 'api/login/login');
Route::rule('api/login/register', 'api/login/register');

// API路由 - 首页
Route::rule('api/index/index', 'api/index/index');
Route::rule('api/index/goods', 'api/index/goods');
Route::rule('api/index/kline', 'api/index/kline');

// API路由 - 用户
Route::rule('api/user/getUserInfo', 'api/user/getUserInfo');
Route::rule('api/user/moneylog', 'api/user/moneylog');
Route::rule('api/user/auth', 'api/user/auth');
Route::rule('api/user/changepwd', 'api/user/changepwd');

// API路由 - 交易
Route::rule('api/order/buy', 'api/order/buy');
Route::rule('api/order/orderlist', 'api/order/orderlist');

// API路由 - 财务
Route::rule('api/finance/recharge_list', 'api/finance/rechargeList');
Route::rule('api/finance/recharge_submit', 'api/finance/rechargeSubmit');
Route::rule('api/finance/recharge_log', 'api/finance/rechargeLog');
Route::rule('api/finance/recharge_callback', 'api/finance/rechargeCallback');
Route::rule('api/finance/withdraw_submit', 'api/finance/withdrawSubmit');
Route::rule('api/finance/withdraw_log', 'api/finance/withdrawLog');
