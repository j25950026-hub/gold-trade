<?php
// 配置文件
return [
    // 服务器地址
    'hostname'        => getenv('DB_HOST') ?: '127.0.0.1',
    // 数据库名
    'database'        => getenv('DB_DATABASE') ?: 'gold_trade',
    // 用户名
    'username'        => getenv('DB_USERNAME') ?: 'root',
    // 密码
    'password'        => getenv('DB_PASSWORD') ?: 'root123',
    // 端口
    'hostport'        => '3306',
    // 数据库编码默认采用utf8
    'charset'         => 'utf8mb4',
    // 数据库表前缀
    'prefix'          => 's_',
    // 数据库调试模式
    'debug'           => true,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否读取主库
    'read_master'     => false,
];
