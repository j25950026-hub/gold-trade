-- ============================================================
-- 秒合约交易系统 - 完整数据库结构
-- 基于ThinkPHP 5 + uni-app H5
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- 管理员表
-- ----------------------------
DROP TABLE IF EXISTS `s_user`;
CREATE TABLE `s_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(10) NOT NULL DEFAULT '' COMMENT '盐',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1正常 0禁用',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='管理员';

-- ----------------------------
-- 会员表
-- ----------------------------
DROP TABLE IF EXISTS `s_member`;
CREATE TABLE `s_member` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '账号',
  `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(10) NOT NULL DEFAULT '' COMMENT '密码盐',
  `real_name` varchar(50) NOT NULL DEFAULT '' COMMENT '真实姓名',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '余额',
  `usdt_money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT 'USDT余额',
  `frozen_money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额',
  `yk` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '累计盈亏',
  `yk_today` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '今日盈亏',
  `credit_score` int(11) NOT NULL DEFAULT '100' COMMENT '信用分',
  `code` varchar(20) NOT NULL DEFAULT '' COMMENT '邀请码',
  `is_auth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '实名认证 0未认证 1认证中 2已认证',
  `id_auth_error` varchar(255) DEFAULT '' COMMENT '认证失败原因',
  `token` varchar(100) NOT NULL DEFAULT '' COMMENT '登录token',
  `logintime` int(11) DEFAULT NULL COMMENT '最后登录',
  `loginip` varchar(50) DEFAULT NULL COMMENT '登录IP',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 1正常 0禁用',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `username` (`username`) USING BTREE,
  KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COMMENT='会员表';

-- ----------------------------
-- 产品分类
-- ----------------------------
DROP TABLE IF EXISTS `s_category`;
CREATE TABLE `s_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '分类名',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='产品分类';

-- ----------------------------
-- 产品表（交易品种）
-- ----------------------------
DROP TABLE IF EXISTS `s_goods`;
CREATE TABLE `s_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '分类ID',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '代码 XAUUSD',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题 国际黄金',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注 Gold',
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '图标',
  `price` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '当前价格',
  `open_price` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '开盘价',
  `vol` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '成交量',
  `zf` varchar(20) NOT NULL DEFAULT '0%' COMMENT '涨跌幅',
  `zf_d` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '涨跌额',
  `is_z` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1涨 2跌',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1显示 0隐藏',
  `is_hot` tinyint(1) NOT NULL DEFAULT '0' COMMENT '热门',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `code` (`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=500 DEFAULT CHARSET=utf8mb4 COMMENT='交易产品';

-- ----------------------------
-- 交易订单表
-- ----------------------------
DROP TABLE IF EXISTS `s_order`;
CREATE TABLE `s_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `code` varchar(50) NOT NULL DEFAULT '' COMMENT '产品代码',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '产品名称',
  `open_price` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '买入价',
  `end_price` decimal(20,4) NOT NULL DEFAULT '0.0000' COMMENT '结算价',
  `number` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '下单金额',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型 1涨 2跌',
  `ploss` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '盈亏金额',
  `seconds` int(11) NOT NULL DEFAULT '180' COMMENT '秒数 60/180/300',
  `profit_ratio` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '收益率%',
  `endTime` bigint(20) NOT NULL DEFAULT '0' COMMENT '到期时间戳(毫秒)',
  `remain_milli_seconds` bigint(20) NOT NULL DEFAULT '0' COMMENT '剩余毫秒',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0进行中 1赢 2输 3平局',
  `buy_time` datetime DEFAULT NULL COMMENT '买入时间',
  `sell_time` datetime DEFAULT NULL COMMENT '结算时间',
  `ip` varchar(50) DEFAULT NULL COMMENT 'IP',
  `end_profit` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '最终盈亏',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `order_sn` (`order_sn`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE,
  KEY `status` (`status`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb4 COMMENT='交易订单';

-- ----------------------------
-- Banner轮播表
-- ----------------------------
DROP TABLE IF EXISTS `s_banner`;
CREATE TABLE `s_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `image` varchar(255) NOT NULL DEFAULT '' COMMENT '图片地址',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='轮播图';

-- ----------------------------
-- 公告表
-- ----------------------------
DROP TABLE IF EXISTS `s_notice`;
CREATE TABLE `s_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL DEFAULT '' COMMENT '标题',
  `content` text COMMENT '内容',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COMMENT='公告';

-- ----------------------------
-- 充值订单
-- ----------------------------
DROP TABLE IF EXISTS `s_recharge`;
CREATE TABLE `s_recharge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `amount` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `type` varchar(20) NOT NULL DEFAULT 'usdt' COMMENT '充值类型',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0待支付 1已完成 2失败',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4 COMMENT='充值记录';

-- ----------------------------
-- 提现订单
-- ----------------------------
DROP TABLE IF EXISTS `s_withdraw`;
CREATE TABLE `s_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '订单号',
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `amount` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '金额',
  `type` varchar(20) NOT NULL DEFAULT 'usdt' COMMENT '提现方式',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '提现地址',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0审核中 1已完成 2已拒绝',
  `remark` varchar(255) DEFAULT NULL COMMENT '拒绝原因',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1000 DEFAULT CHARSET=utf8mb4 COMMENT='提现记录';

-- ----------------------------
-- 资金流水
-- ----------------------------
DROP TABLE IF EXISTS `s_money_log`;
CREATE TABLE `s_money_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
  `money` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '变动金额',
  `before` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '变动前',
  `after` decimal(20,2) NOT NULL DEFAULT '0.00' COMMENT '变动后',
  `memo` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `type` varchar(20) NOT NULL DEFAULT '' COMMENT '类型 order/recharge/withdraw',
  `createtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  KEY `uid` (`uid`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=10000 DEFAULT CHARSET=utf8mb4 COMMENT='资金流水';

-- ----------------------------
-- 网站配置表
-- ----------------------------
DROP TABLE IF EXISTS `s_config`;
CREATE TABLE `s_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL DEFAULT '' COMMENT '配置键',
  `value` text COMMENT '配置值',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `key` (`key`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COMMENT='配置表';

-- ----------------------------
-- 初始数据
-- ----------------------------
INSERT INTO `s_user` VALUES (1, 'admin', '3e0b81839e1c7b2bde10c7951663e104', '2d2eae', 1, NULL, NULL);

INSERT INTO `s_config` VALUES 
(1, 'site_name', '中国黄金', NULL, NULL),
(2, 'kefu_url', 'https://chatlink.mstatik.com/widget/standalone.html?eid=6577e86af3d4432fb2d5d7c6b1f891e1', NULL, NULL),
(3, 'hui_lv', '7.24', NULL, NULL),
(4, 'b_start_time', '00:00', NULL, NULL),
(5, 'b_end_time', '24:00', NULL, NULL),
(6, 'profit_ratio', '3', NULL, NULL),
(7, 'min_bet', '100', NULL, NULL),
(8, 'max_bet', '100000000', NULL, NULL);

INSERT INTO `s_category` VALUES 
(1, '热门', 1, 1, NULL, NULL),
(2, '贵金属', 2, 1, NULL, NULL),
(3, '外汇', 3, 1, NULL, NULL),
(4, '指数', 4, 1, NULL, NULL),
(5, '商品', 5, 1, NULL, NULL);

INSERT INTO `s_goods` VALUES 
(1, 1, 'XAUUSD', '国际黄金', 'Gold', '/static/goods/xau.png', 4580.23, 4523.60, 3122.03, '1.25%', 56.63, 1, 1, 'normal', 1, NULL, NULL),
(2, 2, 'XAGUSD', '国际白银', 'Silver', '/static/goods/xag.png', 76.55, 76.59, 45.75, '-0.05%', -0.04, 2, 2, 'normal', 1, NULL, NULL),
(3, 3, 'EURUSD', '欧元/美元', 'EUR', '/static/goods/eur.png', 1.0876, 1.0870, 1250.00, '0.06%', 0.0006, 1, 3, 'normal', 0, NULL, NULL),
(4, 4, 'USOIL', '美原油', 'Oil', '/static/goods/oil.png', 78.32, 78.65, 895.00, '-0.42%', -0.33, 2, 4, 'normal', 0, NULL, NULL),
(5, 1, 'BTCUSD', '比特币', 'BTC', '/static/goods/btc.png', 62450.00, 61500.00, 1820.00, '1.54%', 950.00, 1, 5, 'normal', 1, NULL, NULL);

INSERT INTO `s_banner` VALUES 
(1, '/static/banner/banner1.png', 1, 1, NULL, NULL),
(2, '/static/banner/banner2.png', 2, 1, NULL, NULL),
(3, '/static/banner/banner3.png', 3, 1, NULL, NULL),
(4, '/static/banner/banner4.png', 4, 1, NULL, NULL),
(5, '/static/banner/banner5.png', 5, 1, NULL, NULL);

INSERT INTO `s_notice` VALUES 
(1, '中国黄金集团公司', '尊敬的用户您好，我司每天提现时间10:00-22:00为反馈新老客户本交易所现推出充值USDT送彩金活动：凡是使用USDT充值的用户，额外赠送百分之五的优惠作为回馈用户！', 1, NULL, NULL);

SET FOREIGN_KEY_CHECKS = 1;
