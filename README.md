# 秒合约交易系统 - 完整部署文档

## 系统架构
- 后端：ThinkPHP 5.0 + MySQL 5.7+
- 前端：uni-app H5（Vue 2.x）
- 管理后台：FastAdmin风格
- 服务器：Nginx + PHP 7.2+

---

## 一、数据库部署

### 1. 创建数据库
```sql
CREATE DATABASE `gold_trade` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. 导入数据表
```bash
mysql -u root -p gold_trade < database/install.sql
```

### 3. 修改数据库配置
编辑 `server/application/database.php`:
```php
return [
    'type'        => 'mysql',
    'hostname'    => '127.0.0.1',
    'database'    => 'gold_trade',
    'username'    => 'root',
    'password'    => 'your_password',
    'hostport'    => '3306',
    'charset'     => 'utf8mb4',
    'prefix'      => 's_',
];
```

---

## 二、后端部署 (ThinkPHP)

### 1. 配置Nginx
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /path/to/server/public;
    index index.php index.html;
    
    location / {
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php/$1 last;
        }
    }
    
    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # API路由（关键配置）
    location /api/ {
        if (!-e $request_filename) {
            rewrite ^(.*)$ /index.php/$1 last;
        }
    }
}
```

### 2. 配置路由
编辑 `server/application/route.php`:
```php
// API路由
Route::rule('api/:controller/:action', 'api/:controller/:action');
```

### 3. 测试API
```bash
# 测试
curl http://yourdomain.com/api/index/index
curl http://yourdomain.com/api/index/goods
# 应该返回JSON数据
```

---

## 三、前端部署 (uni-app H5)

### 1. 构建H5版本
```bash
cd web
npm install
# 在HBuilderX中点击"发行" -> "网站-H5手机版"
```

### 2. 或手动修改manifest.json
- 设置 `h5.router.mode` 为 `hash`
- 设置 `h5.router.base` 为 `/`

### 3. 构建产出在 `web/unpackage/dist/build/h5/`
将文件复制到后端 `server/public/h5/` 目录

### 4. 配置Nginx静态文件
```nginx
location /static/ {
    root /path/to/server/public;
    expires 30d;
}
```

---

## 四、API接口文档

### 公共接口
| 接口 | 方法 | 参数 | 返回 |
|------|------|------|------|
| `/api/index/index` | GET | - | 首页配置(banner/公告/客服/汇率) |
| `/api/index/goods` | GET | - | 产品列表(含实时价格) |
| `/api/index/kline` | GET | goods_id, time | K线数据 |

### 登录注册
| 接口 | 方法 | 参数 | 返回 |
|------|------|------|------|
| `/api/login/login` | POST | account, passwd | {token, status} |
| `/api/login/register` | POST | account, passwd | {massage, status} |

### 用户接口（需Header: token）
| 接口 | 方法 | 说明 |
|------|------|------|
| `/api/user/getUserInfo` | GET | 用户信息(余额/盈亏/信用分) |
| `/api/user/moneylog` | GET | 资金流水 |
| `/api/user/auth` | POST | 实名认证 |
| `/api/user/changepwd` | POST | 修改密码 |

### 交易接口（需Header: token）
| 接口 | 方法 | 参数 |
|------|------|------|
| `/api/order/buy` | POST | goods_id, type(1涨2跌), number, seconds(60/180/300) |
| `/api/order/orderlist` | GET | status(0全部/3已结算) |

### 财务接口（需Header: token）
| 接口 | 方法 | 说明 |
|------|------|------|
| `/api/finance/recharge_submit` | POST | 提交充值 |
| `/api/finance/recharge_log` | GET | 充值记录 |
| `/api/finance/recharge_callback` | POST | 充值回调 |
| `/api/finance/withdraw_submit` | POST | 提交提现 |
| `/api/finance/withdraw_log` | GET | 提现记录 |

---

## 五、管理后台

后台地址: `/admin.php`
默认账号: admin
默认密码: admin123

功能模块：
- 控制台（统计概览）
- 订单管理（交易订单审核/结算）
- 会员管理（用户列表/资金调整）
- 充值管理（审核确认到账）
- 提现管理（审核通过/拒绝）
- 产品管理（产品列表/分类）
- 系统设置（网站配置）
- 新闻公告

---

## 六、定时任务

### 订单过期结算
（已在API中自动处理，下单时结算过期订单）

### 推荐配置
```bash
# 每分钟执行一次，确保订单及时结算
* * * * * curl -s http://yourdomain.com/api/order/settle > /dev/null
```

---

## 七、安全配置

1. 修改默认管理员密码
2. 关闭错误显示 `app_debug => false`
3. 修改后台路径
4. 配置HTTPS
5. 限制登录失败次数

---

## 八、目录结构

```
baidao_full/
├── database/
│   └── install.sql          # 数据库文件
├── server/                   # 后端
│   ├── application/
│   │   ├── api/
│   │   │   └── controller/
│   │   │       ├── Login.php
│   │   │       ├── Index.php
│   │   │       ├── User.php
│   │   │       ├── Order.php
│   │   │       └── Finance.php
│   │   └── admin/
│   │       └── controller/
│   └── public/               # Web入口
├── web/                      # 前端(uni-app)
│   ├── pages.json
│   ├── main.js
│   ├── store.js
│   ├── App.vue
│   ├── pages/
│   │   ├── login/
│   │   ├── index/
│   │   ├── product/
│   │   ├── deal/
│   │   ├── order/
│   │   ├── user/
│   │   ├── recharge/
│   │   └── withdraw/
│   └── static/
│       ├── tab/              # Tab图标
│       └── goods/            # 产品图标
└── README.md
```
