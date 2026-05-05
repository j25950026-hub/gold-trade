<?php
/**
 * 网站配置读取 - 基于FastAdmin框架
 * 对应fa_config表中的配置项
 */

// 读取所有配置
$config = \think\Config::get('site');

// ======== 网站基本配置 ========
$site_name = $config['name'] ?? '中国黄金';          // 站点名称
$version = $config['version'] ?? '1.0.65';           // 版本号
$kefu_url = $config['kefu_url'] ?? '';               // 客服链接
$api_url = $config['api_url'] ?? '';                 // API地址
$wx_appid = $config['wx_appid'] ?? '';               // 微信AppID
$oss_cdn = $config['oss_cdn'] ?? '';                 // OSS CDN

// ======== 交易配置 ========
$min_bet = (float)($config['min_bet'] ?? 100);       // 最小投注
$max_bet = (float)($config['max_bet'] ?? 1000000000); // 最大投注
$trade_start = $config['trade_start'] ?? '00:00';    // 交易开始时间
$trade_end = $config['trade_end'] ?? '24:00';        // 交易结束时间
$contract_rate = (float)($config['contract_rate'] ?? 92); // 收益率%
$recharge_gift = (float)($config['recharge_gift'] ?? 0);  // 充值赠送%
$invite_reward = (float)($config['invite_reward'] ?? 0);  // 邀请奖励

// ======== 提现配置 ========
$withdraw_min = (float)($config['withdraw_min'] ?? 100);
$withdraw_max = (float)($config['withdraw_max'] ?? 500000);
$withdraw_fee = (float)($config['withdraw_fee'] ?? 0);

// ======== 风控配置 ========
$register_switch = (int)($config['register_switch'] ?? 1);  // 注册开关 1开0关
$blacklist_jump = $config['blacklist_jump'] ?? 'https://www.baidu.com/'; // 黑名单跳转
$online_user_min = (int)($config['online_user_min'] ?? 50);  // 假在线下限
$online_user_max = (int)($config['online_user_max'] ?? 300);  // 假在线上限
$login_failure_retry = (int)($config['fastadmin.login_failure_retry'] ?? true);
$login_captcha = (int)($config['fastadmin.login_captcha'] ?? true);
