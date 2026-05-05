<?php
/**
 * 首页API
 * GET /api/index/index
 * GET /api/index/goods
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class Index extends Controller
{
    /**
     * 首页数据（banner、公告、配置）
     */
    public function index()
    {
        $kefuUrl = Db::name('config')->where('key', 'kefu_url')->value('value') ?: '';
        $siteName = Db::name('config')->where('key', 'site_name')->value('value') ?: '中国黄金';
        $huiLv = Db::name('config')->where('key', 'hui_lv')->value('value') ?: '7.24';
        $startTime = Db::name('config')->where('key', 'b_start_time')->value('value') ?: '00:00';
        $endTime = Db::name('config')->where('key', 'b_end_time')->value('value') ?: '24:00';
        
        // 公告
        $notice = Db::name('notice')->where('status', 1)->order('id', 'desc')->find();
        
        // Banner
        $banners = Db::name('banner')->where('status', 1)->order('sort', 'asc')->select();
        
        // 如果用户已登录，拼接待用户名
        $userId = $this->getUserId();
        $userInfo = '';
        if ($userId) {
            $user = Db::name('member')->find($userId);
            if ($user) {
                $userInfo = "账号：{$user['username']},姓名:{$user['real_name']},可用余额:{$user['money']}&id={$user['id']}";
            }
        }
        
        $kefuUrlFull = $kefuUrl;
        if ($userInfo) {
            $kefuUrlFull = $kefuUrl . '?name=' . urlencode($userInfo);
        }
        
        return json([
            'massage' => '',
            'data'    => [
                'kefu_url'    => $kefuUrlFull ?: $kefuUrl,
                'gg_title'    => $notice['title'] ?? $siteName,
                'gg_con'      => $notice['content'] ?? '',
                'gg_time'     => $notice['createtime'] ? date('Y-m-d H:i:s', $notice['createtime']) : '',
                'hui_lv'      => $huiLv,
                'b_start_time' => $startTime,
                'b_end_time'   => $endTime,
                'b_is'         => 0,
                'bannerList'   => $banners ?: []
            ],
            'status'  => 1
        ]);
    }
    
    /**
     * 产品列表（行情）
     * GET /api/index/goods
     */
    public function goods()
    {
        $goods = Db::name('goods')
            ->where('status', 1)
            ->order('sort', 'asc')
            ->select();
        
        // 生成随机价格波动
        foreach ($goods as &$g) {
            $randChange = (mt_rand(-200, 200) / 10000) * ($g['price'] ?: 100);
            $newPrice = $g['price'] + $randChange;
            $zf = $g['open_price'] > 0 ? ($newPrice - $g['open_price']) / $g['open_price'] * 100 : 0;
            
            $g['price'] = round($newPrice, 4);
            $g['zf'] = sprintf('%.2f%%', $zf);
            $g['zf_d'] = round($newPrice - $g['open_price'], 4);
            $g['is_z'] = $zf >= 0 ? 1 : 2;
            $g['vol'] = round($g['vol'] + mt_rand(-100, 100) / 10, 2);
        }
        
        return json(['massage' => '', 'data' => $goods, 'status' => 1]);
    }
    
    /**
     * K线数据
     * GET /api/index/kline?goods_id=349&time=1min
     */
    public function kline()
    {
        $goodsId = $this->request->get('goods_id', 0);
        $time = $this->request->get('time', '1min');
        
        if (!$goodsId) {
            return json(['massage' => '参数错误', 'data' => [], 'status' => -1]);
        }
        
        $good = Db::name('goods')->find($goodsId);
        $basePrice = $good ? $good['price'] : 4500;
        
        $interval = ['1min' => 60000, '3min' => 180000, '5min' => 300000];
        $ms = $interval[$time] ?? 60000;
        $count = 100;
        $now = time() * 1000;
        
        $data = [];
        $price = (float)$basePrice;
        for ($i = $count; $i >= 0; $i--) {
            $t = $now - $i * $ms;
            $change = (mt_rand(-50, 50) / 1000) * $price;
            $open = $price;
            $close = $price + $change;
            $high = max($open, $close) + abs($change) * 0.3;
            $low = min($open, $close) - abs($change) * 0.3;
            
            $data[] = [
                'time'   => $t,
                'open'   => round($open, 2),
                'high'   => round($high, 2),
                'low'    => round($low, 2),
                'close'  => round($close, 2),
                'volume' => mt_rand(100, 10000)
            ];
            $price = $close;
        }
        
        return json(['massage' => '', 'data' => ['kline' => $data], 'status' => 1]);
    }
    
    private function getUserId()
    {
        $token = $this->request->header('token');
        if (!$token) return 0;
        $user = Db::name('member')->where('token', $token)->find();
        return $user ? $user['id'] : 0;
    }
}
