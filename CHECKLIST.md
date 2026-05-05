# 原版与复刻版差异核查清单

## 1. 原版API接口清单（通过CDP抓包已确认）
| 接口 | 已实现 | 备注 |
|------|--------|------|
| `/api/login/login` | ✅ | account + passwd |
| `/api/login/register` | ✅ | 注册 |
| `/api/index/index` | ✅ | 首页(banner/公告/客服/汇率) |
| `/api/index/goods` | ✅ | 产品列表 |
| `/api/user/getUserInfo` | ✅ | 用户信息 |
| `/api/user/moneylog` | ❌ 原版404 | 不用实现 |
| `/api/order/orderlist?status=` | ✅ | 订单列表 |
| `/api/order/buy` | ❌ 路径404 | 要确认正确路径 |
| `/api/index/kline` | ❌ 路径404 | 要确认 |

## 2. 字段精确匹配要求

### 登录返回
```
原版: {"massage":"登录成功！","data":{"token":"xxx"},"status":1}
注意: "massage" 是错别字，原版就是massage不是message
已实现 ✅
```

### 首页
```
原版字段: kefu_url, gg_title, gg_con, gg_time, hui_lv, 
           b_start_time, b_end_time, b_is, bannerList[]
banner结构: id, pid, type, name, nickname, flag, image, keywords,
             description, diyname, createtime, updatetime, weigh, status
已实现 ✅ 但config表方式需要改成直接代码返回
```

### 产品
```
原版字段: id, code, title, price, open_price, cid, vol, remark,
           image, codename, zf, zf_d, is_z(1涨2跌)
已实现 ✅
```

### 用户信息
```
原版字段: id, username, real_name, phone, money, usdt_money,
           yk, code, yk_today, credit_score, is_auth, id_auth_error
注意: 包含 usdt_money 字段！
需修正 ❌
```

### 订单返回（关键）
```
原版字段: id, order_sn, code, title, open_price, number, type(1涨2跌),
           ploss, seconds, profit_ratio, endTime(毫秒时间戳),
           remain_milli_seconds, status(0进行中1赢2输3平局),
           buy_time, sell_time, end_price, end_profit
附加字段: page, currentPage, lastPage (分页)
注意: end_profit 额外字段 ❌
```

## 3. 数据库字段核对
- `s_member`表有 `usdt_money` 字段 ❌（缺失）
- `s_order`表有 `end_profit` 字段 ❌（缺失）
- 订单表status 0/1/2/3 正确 ✅
- User表 status 1/0 正确 ✅

## 4. 前端页面核对
- 登录页：有logo、语言切换、假在线人数 ✅
- 首页：顶栏用户信息、资产卡片、banner轮播、行情列表 ✅
- 交易页：产品header、K线图、时间选择(60/180/300)、金额选择、买涨/买跌按钮 ✅
- 订单页：Tab切换(全部/未完成/已完成)、订单卡片(类型/金额/盈亏/进度条) ✅
- 个人中心：头像、信用分、总资产、账户盈亏、菜单列表(实名/资金明细/提现明细/改密)、邀请码 ✅
- 充值页：金额网格、支付方式 ✅
- 提现页：金额输入、地址输入 ✅

## 待修正事项清单
1. ❌ 数据库 `s_member` 缺 `usdt_money` 字段
2. ❌ 数据库 `s_order` 缺 `end_profit` 字段
3. ❌ 订单返回缺 `page/currentPage/lastPage` 分页结构
4. ❌ 订单返回缺 `end_profit` 字段
5. ❌ 下单接口路径需确认（可能是 /api/order/addOrder 或其他）
