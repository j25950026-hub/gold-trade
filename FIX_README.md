# 部署修复说明 - PHP 8.3兼容ThinkPHP 5.0修复

## 问题
ThinkPHP 5.0.24 使用 `preg_replace_callback()` 时传入 `null` 作为 `$subject`，
这在 PHP 8.1+ 会触发 Deprecation Warning，导致框架报错中断。

## 修复方案（任选一个）

### 方案A：替换index.php（推荐，不影响其他项目）
```bash
cat > /var/www/gold-trade/server/public/index.php << 'PHPEOF'
<?php
// 抑制PHP 8.1+废弃警告（兼容ThinkPHP 5.0）
error_reporting(E_ALL & ~E_DEPRECATED & ~E_WARNING);
// [ 应用入口文件 ]
define('APP_PATH', __DIR__ . '/../application/');
define('BIND_MODULE', 'api');
// 加载框架引导文件
require __DIR__ . '/../vendor/topthink/framework/start.php';
PHPEOF

systemctl restart php8.3-fpm
curl -s http://localhost:8088/api/index/index
```

### 方案B：修复框架本身（一劳永逸）
```bash
sed -i 's/$name = preg_replace_callback/$name = preg_replace_callback((array)$name/; s/}, $name);/}, $name);/' /var/www/gold-trade/server/vendor/topthink/framework/library/think/Loader.php

systemctl restart php8.3-fpm
curl -s http://localhost:8088/api/index/index
```

### 方案C：降低PHP版本到7.4（最稳定）
```bash
apt install -y php7.4 php7.4-fpm php7.4-mysql php7.4-mbstring php7.4-gd php7.4-curl php7.4-xml php7.4-cli
update-alternatives --set php /usr/bin/php7.4

# 配Nginx用PHP 7.4
sed -i 's/php8.3-fpm/php7.4-fpm/' /etc/nginx/sites-enabled/gold.conf

systemctl stop php8.3-fpm
systemctl start php7.4-fpm
systemctl restart nginx
curl -s http://localhost:8088/api/index/index
```

## 最终测试
成功的话会返回JSON格式的首页数据。
